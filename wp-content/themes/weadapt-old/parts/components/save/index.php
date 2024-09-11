<style>
    .save {
        margin-top: -3rem !important;
        margin-bottom: 2rem !important;
    }
</style>
<div class="container save">
    <?php
    echo get_button([
        'url' => '#',
        'title' => __('Save', 'weadapt'),
        'target' => '',
        'attributes' => [
            'data-profile-save' => '',
        ]
    ]);
    ?>
</div>