<?php if ( has_nav_menu( 'header-top-menu' ) ) : ?>

<div class="main-header__top-area">
	<div class="container">
		<nav class="main-header__nav main-header__nav--top mb-popup-content" aria-label="<?php _e( 'Additional Navigation', 'weadapt' ); ?>">
			<ul class="menu">
				<?php
					wp_nav_menu( [
						'theme_location' => 'header-top-menu',
						'container'      => false,
						'walker'         => new Card_Walker_Nav_Menu(),
						'items_wrap'     => '%3$s',
					] );
				?>

				<?php if ( get_field( 'languages_controller', 'options' ) ) : ?>
					<li class="menu-item">
						<span class="menu-item__wrap">
							<span class="menu-item__icon"><?php echo get_img( 'icon-glob' ); ?></span>

							<button translate="no" class="menu-item--lang"><?php _e( 'En', 'weadapt' ); ?></button>

							<button class="menu-item__dropdown" aria-expanded="false" aria-haspopup="true" type="button">
								<span class="screen-reader-text"><?php _e( 'Translate Submenu', 'weadapt' ); ?></span>
								<?php echo get_img( 'icon-chevron-down' ); ?>
							</button>
						</span>

						<div class="mega-menu mega-menu--lang">
							<div class="container" tabindex="-1">
								<?php
									$languages = [
										"af"    => "Afrikaans",
										"sq"    => "Albanian",
										"am"    => "Amharic",
										"ar"    => "Arabic",
										"hy"    => "Armenian",
										"az"    => "Azerbaijani",
										"eu"    => "Basque",
										"be"    => "Belarusian",
										"bn"    => "Bengali",
										"bs"    => "Bosnian",
										"bg"    => "Bulgarian",
										"ca"    => "Catalan",
										"ceb"   => "Cebuano",
										"zh-CN" => "Chinese (Simplified)",
										"zh-TW" => "Chinese (Traditional)",
										"co"    => "Corsican",
										"hr"    => "Croatian",
										"cs"    => "Czech",
										"da"    => "Danish",
										"nl"    => "Dutch",
										"en"    => "English",
										"eo"    => "Esperanto",
										"et"    => "Estonian",
										"fil"   => "Filipino (Tagalog)",
										"fi"    => "Finnish",
										"fr"    => "French",
										"fy"    => "Frisian",
										"gl"    => "Galician",
										"ka"    => "Georgian",
										"de"    => "German",
										"el"    => "Greek",
										"gu"    => "Gujarati",
										"ht"    => "Haitian Creole",
										"ha"    => "Hausa",
										"haw"   => "Hawaiian",
										"iw"    => "Hebre",
										"hi"    => "Hindi",
										"hmn"   => "Hmong",
										"hu"    => "Hungarian",
										"is"    => "Icelandic",
										"ig"    => "Igbo",
										"id"    => "Indonesian",
										"ga"    => "Irish",
										"it"    => "Italian",
										"ja"    => "Japanese",
										"jw"    => "Javanes",
										"kn"    => "Kannada",
										"kk"    => "Kazakh",
										"km"    => "Khmer",
										"rw"    => "Kinyarwanda",
										"ko"    => "Korean",
										"ku"    => "Kurdish",
										"ckb"   => "Kurdish (Sorani)",
										"ky"    => "Kyrgyz",
										"lo"    => "Lao",
										"la"    => "Latin",
										"lv"    => "Latvian",
										"lt"    => "Lithuanian",
										"lb"    => "Luxembourgish",
										"mk"    => "Macedonian",
										"mg"    => "Malagasy",
										"ms"    => "Malay",
										"ml"    => "Malayalam",
										"mt"    => "Maltese",
										"mi"    => "Maori",
										"mr"    => "Marathi",
										"mn"    => "Mongolian",
										"my"    => "Myanmar (Burmese)",
										"ne"    => "Nepali",
										"no"    => "Norwegian",
										"ny"    => "Nyanja (Chichewa)",
										"or"    => "Odia (Oriya)",
										"ps"    => "Pashto",
										"fa"    => "Persian",
										"pl"    => "Polish",
										"pt"    => "Portuguese (Portugal)",
										"pa"    => "Punjabi",
										"ro"    => "Romanian",
										"ru"    => "Russian",
										"sm"    => "Samoan",
										"gd"    => "Scots Gaelic",
										"sr"    => "Serbian",
										"st"    => "Sesotho",
										"sn"    => "Shona",
										"sd"    => "Sindhi",
										"si"    => "Sinhala (Sinhalese)",
										"sk"    => "Slovak",
										"sl"    => "Slovenian",
										"so"    => "Somali",
										"es"    => "Spanish",
										"su"    => "Sundanese",
										"sw"    => "Swahili",
										"sv"    => "Swedish",
										"tl"    => "Tagalog (Filipino)",
										"tg"    => "Tajik",
										"ta"    => "Tamil",
										"tt"    => "Tatar",
										"te"    => "Telugu",
										"th"    => "Thai",
										"tr"    => "Turkish",
										"tk"    => "Turkmen",
										"uk"    => "Ukrainian",
										"ur"    => "Urdu",
										"ug"    => "Uyghur",
										"uz"    => "Uzbek",
										"vi"    => "Vietnamese",
										"cy"    => "Welsh",
										"xh"    => "Xhosa",
										"yi"    => "Yiddish",
										"yo"    => "Yoruba",
										"zu"    => "Zulu"
									];
								?>
								<ul>
									<?php foreach ( $languages as $code => $name ) : ?>
										<li>
											<?php echo get_img( 'icon-glob' ); ?>
											<span data-google-lang="<?php echo esc_attr( $code ); ?>" tabindex="0" translate="no"><?php esc_html_e( $name, 'weadapt' ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
								<select>
									<?php foreach ( $languages as $code => $name ) : ?>
										<option data-google-lang="<?php echo esc_attr( $code ); ?>" value="<?php echo esc_attr( $code ); ?>"><?php esc_html_e( $name, 'weadapt' ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</li>
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</div>

<?php endif; ?>