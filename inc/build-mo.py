
import struct
import os

langs = {
    'zh_CN': {
        'Sticky Posts': '置顶精彩',
        ' ago': '前',
        '0 Comments': '0评论',
        '1 Comment': '1评论',
        '% Comments': '%评论',
        'Views': '浏览',
        'Previous': '上一篇',
        'Next': '下一篇',
        'Related Posts': '相关文章',
        'Search': '搜索',
        'Comments': '评论',
        'Reply': '回复',
        'Post Comment': '发布评论',
        'Name': '昵称',
        'Website': '网站地址',
        'Page Not Found': '看来您迷路了',
        'We cannot find the page you are looking for, ': '我们找不到您想要的页面，',
        'Return to Home': '返回首页',
        'Search Results for: %s': '搜索"%s"的结果！',
        'Load More': '加载更多',
        'Main Menu': '主菜单导航',
        'Mobile Menu': '手机导航',
    },
    'zh_TW': {
        'Sticky Posts': '置頂精選',
        ' ago': '前',
        '0 Comments': '0 留言',
        '1 Comment': '1 留言',
        '% Comments': '% 留言',
        'Views': '瀏覽',
        'Previous': '上一篇',
        'Next': '下一篇',
        'Related Posts': '相關文章',
        'Search': '搜尋',
        'Comments': '留言',
        'Reply': '回覆',
        'Post Comment': '發佈留言',
        'Name': '暱稱',
        'Website': '網站地址',
        'Page Not Found': '看來您迷路了',
        'We cannot find the page you are looking for, ': '我們找不到您想要的頁面，',
        'Return to Home': '返回首頁',
        'Search Results for: %s': '搜尋 "%s" 的結果！',
        'Load More': '載入更多',
        'Main Menu': '主選單導航',
        'Mobile Menu': '手機導航',
    },
    'ja': {
        'Sticky Posts': 'おすすめ',
        ' ago': '前',
        '0 Comments': '0 コメント',
        '1 Comment': '1 コメント',
        '% Comments': '% コメント',
        'Views': '閲覧',
        'Previous': '前へ',
        'Next': '次へ',
        'Related Posts': '関連記事',
        'Search': '検索',
        'Comments': 'コメント',
        'Reply': '返信',
        'Post Comment': 'コメントを投稿',
        'Name': '名前',
        'Website': 'ウェブサイト',
        'Page Not Found': 'ページが見つかりません',
        'We cannot find the page you are looking for, ': 'お探しのページは見つかりませんでした。',
        'Return to Home': 'ホームに戻る',
        'Search Results for: %s': '「%s」の検索結果',
        'Load More': 'もっと見る',
        'Main Menu': 'メインメニュー',
        'Mobile Menu': 'モバイルメニュー',
    }
}

languages_dir = os.path.join(os.path.dirname(__file__), '../languages')
if not os.path.exists(languages_dir):
    os.makedirs(languages_dir)

def write_mo(filename, translations):
    # Sort keys for binary search (standard behavior)
    keys = sorted(translations.keys())
    count = len(keys)
    
    # Calculate offsets
    header_size = 28
    original_table_offset = header_size
    trans_table_offset = header_size + (count * 8)
    hash_table_offset = header_size + (count * 8) * 2
    
    string_start_offset = hash_table_offset # No hash table for simplicity
    
    # Prepare string data
    originals_data = b''
    translations_data = b''
    
    original_table = []
    trans_table = []
    
    current_offset = string_start_offset
    
    # Pass 1: Originals
    for k in keys:
        encoded = k.encode('utf-8') + b'\0'
        original_table.append((len(encoded) - 1, current_offset))
        originals_data += encoded
        current_offset += len(encoded)
        
    # Pass 2: Translations
    for k in keys:
        val = translations[k]
        encoded = val.encode('utf-8') + b'\0'
        trans_table.append((len(encoded) - 1, current_offset))
        translations_data += encoded
        current_offset += len(encoded)

    with open(filename, 'wb') as f:
        # Header
        f.write(struct.pack('I', 0x950412de)) # magic
        f.write(struct.pack('I', 0))          # revision
        f.write(struct.pack('I', count))      # count
        f.write(struct.pack('I', original_table_offset)) # offset originals
        f.write(struct.pack('I', trans_table_offset))    # offset translations
        f.write(struct.pack('I', 0))          # hash table size
        f.write(struct.pack('I', hash_table_offset))     # offset hash table
        
        # Original Table
        for length, offset in original_table:
            f.write(struct.pack('II', length, offset))
            
        # Translation Table
        for length, offset in trans_table:
            f.write(struct.pack('II', length, offset))
            
        # Strings
        f.write(originals_data)
        f.write(translations_data)

for lang, trans in langs.items():
    path = os.path.join(languages_dir, f'{lang}.mo')
    write_mo(path, trans)
    print(f'Generated {path}')
