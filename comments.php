<?php
if ( post_password_required() )
    return;
?>
<div id="comments" class="comments-area">
    <div class="layoutSingleColumn">
        <?php if ( have_comments() ) : ?>
            <h3 class="comments-title"><i class="bi bi-filter me-2"></i><?php _e('Comments', 'mango'); ?><small>(<?php echo number_format_i18n( get_comments_number() );?>)</small></h3>
            <ol class="comment-list">
                <?php
                wp_list_comments( array(
                    'style'         => 'ol',
                    'short_ping'    => true,
                    'reply_text'    => __('Reply', 'mango'),
                    'avatar_size'   => 40,
                    'format'        => 'html5'
                ) );
                ?>
            </ol>
            <?php the_comments_pagination( array(
                'prev_text' => __('Previous', 'mango'),
                'next_text' => __('Next', 'mango'),
                'prev_next' => false,
            ) );?>
        <?php endif; ?>
        <?php
        $comments_args = array(
        'label_submit'=> __('Post Comment', 'mango'),
        'title_reply'=>'<i class="bi bi-keyboard me-1"></i>' . __('Post Comment', 'mango'),
        'comment_form_top' => 'ds',
        'comment_notes_before' => '',
        'comment_notes_after' => '',
        'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true"></textarea></p>',
        'fields' => apply_filters( 'comment_form_default_fields', array(
        'author' =>
        '<p class="comment-form-author">'  .
        '<input id="author" class="blog-form-input" placeholder="' . __('Name', 'mango') . '" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
        '" size="30" /></p>',
        'email' =>
        '<p class="comment-form-email">'.
        '<input id="email" class="blog-form-input" placeholder="Email " name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
        '" size="30" /></p>',
        'url' =>
        '<p class="comment-form-url">'.
        '<input id="url" class="blog-form-input" placeholder="' . __('Website', 'mango') . '" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
        '" size="30" /></p>'
        )
        ),
        );
        comment_form($comments_args);?>
    </div>
</div>