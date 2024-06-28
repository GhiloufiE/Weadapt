<div class="main-footer__widget__list"><?php
	foreach (range(1, 3) as $i ) :
		if ( is_active_sidebar( "footer-area-$i" ) ) :
			?><div class="main-footer__widget"><?php dynamic_sidebar( "footer-area-$i" ); ?></div><?php
		endif;
	endforeach;
?></div>