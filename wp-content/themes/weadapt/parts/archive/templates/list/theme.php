<?php

$post_ID = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;

if ( ! empty( $post_ID ) ) :
    // Get the case study count
    $case_study_count = get_post_meta_count( $post_ID, ['case-study'], 'Case study', 'Case studies' );

    // Initialize the post_meta array
    $post_meta = [
        ['icon-user', get_members_count( $post_ID )],
        ['icon-edit-pencil', get_post_meta_count( $post_ID )],
    ];

    // Only add the case study if the count is greater than 0
    if ( $case_study_count > 0 ) {
        $post_meta[] = ['icon-glob', $case_study_count];
    }
?>

<article class="cpt-list-item theme-list-item">
    <?php the_post_thumbnail_html( $post_ID ); ?>

    <div class="cpt-list-item__content">
        <?php
            the_post_type_html( $post_ID );
            the_post_title_html( $post_ID );
            the_post_excerpt_html( $post_ID );
            the_post_meta_html( $post_meta );  // This will only include the case study count if it is greater than 0
            /* the_post_tag_html( $post_ID ); */
        ?>

        <div class="cpt-list-item__actions">
            <?php get_part( 'components/button-join/index', [
                'style' => 'outline'
            ] ); ?>

            <?php get_part( 'components/button-share/index', [
                'url' => get_permalink( $post_ID ),
                'type' => get_post_type( $post_ID )
            ] ); ?>
        </div>
    </div>
</article>

<?php endif; ?>