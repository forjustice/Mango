<?php
// Simple script to generate MO files for Mango theme i18n
// Usage: php inc/build-mo.php

$languages_dir = __DIR__ . '/../languages';
if (!is_dir($languages_dir)) {
    mkdir($languages_dir, 0755, true);
}

$translations = [
    'zh_CN' => [
        'Sticky Posts' => '置顶精彩',
        ' ago' => '前',
        '0 Comments' => '0评论',
        '1 Comment' => '1评论',
        '% Comments' => '%评论',
        'Views' => '浏览',
        'Previous' => '上一篇',
        'Next' => '下一篇',
        'Related Posts' => '相关文章',
        'Search' => '搜索',
        'Comments' => '评论',
        'Reply' => '回复',
        'Post Comment' => '发布评论',
        'Name' => '昵称',
        'Website' => '网站地址',
        'Page Not Found' => '看来您迷路了',
        'We cannot find the page you are looking for, ' => '我们找不到您想要的页面，',
        'Return to Home' => '返回首页',
        'Search Results for: %s' => '搜索"%s"的结果！',
        'Load More' => '加载更多',
        'Main Menu' => '主菜单导航',
        'Mobile Menu' => '手机导航',
    ],
    'zh_TW' => [
        'Sticky Posts' => '置頂精選',
        ' ago' => '前',
        '0 Comments' => '0 留言',
        '1 Comment' => '1 留言',
        '% Comments' => '% 留言',
        'Views' => '瀏覽',
        'Previous' => '上一篇',
        'Next' => '下一篇',
        'Related Posts' => '相關文章',
        'Search' => '搜尋',
        'Comments' => '留言',
        'Reply' => '回覆',
        'Post Comment' => '發佈留言',
        'Name' => '暱稱',
        'Website' => '網站地址',
        'Page Not Found' => '看來您迷路了',
        'We cannot find the page you are looking for, ' => '我們找不到您想要的頁面，',
        'Return to Home' => '返回首頁',
        'Search Results for: %s' => '搜尋 "%s" 的結果！',
        'Load More' => '載入更多',
        'Main Menu' => '主選單導航',
        'Mobile Menu' => '手機導航',
    ],
    'ja' => [
        'Sticky Posts' => 'おすすめ',
        ' ago' => '前',
        '0 Comments' => '0 コメント',
        '1 Comment' => '1 コメント',
        '% Comments' => '% コメント',
        'Views' => '閲覧',
        'Previous' => '前へ',
        'Next' => '次へ',
        'Related Posts' => '関連記事',
        'Search' => '検索',
        'Comments' => 'コメント',
        'Reply' => '返信',
        'Post Comment' => 'コメントを投稿',
        'Name' => '名前',
        'Website' => 'ウェブサイト',
        'Page Not Found' => 'ページが見つかりません',
        'We cannot find the page you are looking for, ' => 'お探しのページは見つかりませんでした。',
        'Return to Home' => 'ホームに戻る',
        'Search Results for: %s' => '「%s」の検索結果',
        'Load More' => 'もっと見る',
        'Main Menu' => 'メインメニュー',
        'Mobile Menu' => 'モバイルメニュー',
    ],
];

class SimpleMO {
    public static function write($file, $entries) {
        $count = count($entries);
        $originals = array_keys($entries);
        $translations = array_values($entries);
        
        // Header
        $magic = 0x950412de;
        $revision = 0;
        
        // Calculate offsets
        $header_size = 28;
        $strings_offset = $header_size + ($count * 8) * 2; // Originals table + Translations table
        
        $output_originals = '';
        $output_translations = '';
        $table_originals = '';
        $table_translations = '';
        
        $current_offset = $strings_offset;
        
        // Sort entries to be able to use binary search (standard requirement for MO, though WP might not strictly enforce it for small files)
        // Actually, let's keep it simple. wp-includes/pomo/mo.php supports unsorted but hash table approach is better.
        // For simplicity in this script, we'll write them sequentially.
        
        foreach ($entries as $orig => $trans) {
            // Original string
            $len = strlen($orig);
            $table_originals .= pack('LL', $len, $current_offset);
            $output_originals .= $orig . "\0";
            $current_offset += $len + 1;
        }
        
        foreach ($entries as $orig => $trans) {
             // Translation string
            $len = strlen($trans);
            $table_translations .= pack('LL', $len, $current_offset);
            $output_translations .= $trans . "\0";
            $current_offset += $len + 1;
        }

        // Write file
        $fp = fopen($file, 'wb');
        if (!$fp) return false;
        
        fwrite($fp, pack('L', $magic)); // magic
        fwrite($fp, pack('L', $revision)); // revision
        fwrite($fp, pack('L', $count)); // count
        fwrite($fp, pack('L', $header_size)); // offset originals
        fwrite($fp, pack('L', $header_size + ($count * 8))); // offset translations
        fwrite($fp, pack('L', 0)); // size of hash table
        fwrite($fp, pack('L', $header_size + ($count * 8) * 2)); // offset hash table
        
        fwrite($fp, $table_originals);
        fwrite($fp, $table_translations);
        fwrite($fp, $output_originals);
        fwrite($fp, $output_translations);
        
        fclose($fp);
        return true;
    }
}

echo "Generating MO files...\n";
foreach ($translations as $lang => $entries) {
    if (SimpleMO::write($languages_dir . "/$lang.mo", $entries)) {
        echo "Generated $lang.mo\n";
    } else {
        echo "Failed to generate $lang.mo\n";
    }
}
echo "Done.\n";
