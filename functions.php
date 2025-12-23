<?php

//加载语言包
function mango_setup() {
    load_theme_textdomain( 'mango', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'mango_setup' );

//ajax评论
require get_template_directory(). '/inc/comment/main.php';

//缩略图裁剪
require get_template_directory(). '/inc/thumbnails.php';

//分类图像
require get_template_directory(). '/inc/categories-images.php';

//wp优化
require get_template_directory(). '/inc/index.php';

//基础
require get_template_directory(). '/inc/norm.php';

//注册导航
register_nav_menus(
	array(
	'main'     => __( 'Main Menu', 'mango' ),
	'mob'      => __( 'Mobile Menu', 'mango' ),
	)
);

//小工具
require get_template_directory(). '/inc/widget.php';
