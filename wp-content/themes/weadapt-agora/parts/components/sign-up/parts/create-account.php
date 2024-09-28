<div class="row">
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-first-name"><?php _e('First Name', 'weadapt'); ?><span class="required">*</span></label>
			<input id="user-first-name" type="text" name="user_first_name" required="required" autocomplete="given-name">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-last-name"><?php _e('Last Name', 'weadapt'); ?><span class="required">*</span></label>
			<input id="user-last-name" type="text" name="user_last_name" required="required" autocomplete="additional-name">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="create-user-name"><?php _e('Username', 'weadapt'); ?><span class="required">*</span></label>
			<input id="create-user-name" type="text" name="user_name" required="required" autocomplete="nickname">
			<span class="description"><?php _e('Spaces are allowed; punctuation is not allowed except for periods, hyphens, apostrophes, and underscores.', 'weadapt'); ?></span>
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="create-user-email"><?php _e('E-mail address', 'weadapt'); ?><span class="required">*</span></label>
			<input id="create-user-email" type="email" name="user_email" required="required" autocomplete="email">
			<span class="description"><?php _e('A valid e-mail address. All e-mails from the system will be sent to this address. The e-mail address is not made public and will only be used if you wish to receive a new password or certain notifications by e-mail.', 'weadapt'); ?></span>
		</p>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-pass"><?php _e('Password', 'weadapt'); ?><span class="required">*</span></label>
			<input id="user-pass" class="user-pass" type="password" name="user_pass" required="required" autocomplete="current-password">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<div class="ajax-form__pass-strength ajax-form__description">
			<div class="ajax-form__pass-strength__line">
				<?php _e('Password strength:', 'weadapt'); ?>
				<span></span>
			</div>
			<div class="ajax-form__pass-strength__status"></div>
		</div>
	</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-pass-confirm"><?php _e('Confirm Password', 'weadapt'); ?><span class="required">*</span></label>
			<input id="user-pass-confirm" class="user-pass" type="password" name="user_pass_confirm" required="required" autocomplete="current-password">
		</p>
	</div>
	<div class="col-12 col-md-6">
		<span class="ajax-form__description"><?php _e('Provide a password for the new account in both fields.', 'weadapt'); ?></span>
	</div>
	<div class="col-12 col-md-6">
		<div class="ajax-form__field">
			<label for="user-roles"><?php _e('Roles', 'weadapt'); ?></label>
			<?php
			$terms = get_terms(
				array(
					'taxonomy' => 'role',
					'hide_empty' => false,
				)
			);

			if (!empty($terms) && !is_wp_error($terms)) {
				echo '<div class="custom-multiselect">';
				echo '<div class="selected-options" id="selected-options"></div>';
				echo '<select id="user-roles" multiple="multiple" name="role[]" class="styled-multiselect">';
				foreach ($terms as $term) {
					echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}
			$args = [
				'post_status'    => ['publish'],
				'post_type'      => 'organisation',
				'posts_per_page' => -1,
			];

			$query = new WP_Query($args);
			?>
		</div>
	</div>

	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<label for="user-country"><?php _e('Country', 'weadapt'); ?><span class="required">*</span></label>
			<?php
			$field_key = "field_6437a20cbbc21";
			$field = get_field_object($field_key);

			if ($field) {
				echo '<select id="user-country" name="' . esc_attr($field['name']) . '" class="styled-select" required="required">';
				echo '<option value="">' . __('Select Country', 'weadapt') . '</option>';
				foreach ($field['choices'] as $value => $label) {
					echo '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
				}
				echo '</select>';
			}
			?>
		</p>
	</div>

	<div class="col-12 col-md-6">
    <p class="ajax-form__field">
        <?php $field_key = "field_6437a28bbbc22";
        $field = get_field_object($field_key);
        ?>
        <label for="user-town-city"><?php _e('Town/City', 'weadapt'); ?><span class="required">*</span></label>
        <?php if ($field) {
            echo '<input id="user-town-city" type="text" name="' . esc_attr($field['name']) . '" required="required">';
        }
        ?>
    </p>
</div>
	<div class="col-12 col-md-6">
		<p class="ajax-form__field">
			<?php $field_key = "field_6437a2a4bbc23";
			$field = get_field_object($field_key);
			?>
			<label for="user-county"><?php _e('County', 'weadapt'); ?></label>
			<?php if ($field) {
				echo '<input id="user-county" type="text" name="' . esc_attr($field['name']) . '" >';
			}
			?>
		</p>
	</div>

	<div class="col-12">
    <div class="register-profile__item">
        <h4 class="register-profile__title"><?php _e('Organisation', 'weadapt'); ?></h4>
        <div class="theme-select-wrap">
            <input type="text" id="organisation-search" placeholder="<?php _e('Search organisation...', 'weadapt'); ?>" onkeyup="filterOrganisations()">
            <div id="organisation-list" class="styled-checkbox-list">
                <?php while ($query->have_posts()) :
                    $query->the_post();
                    $ID = get_the_ID();
                ?>
                    <label class="organisation-item">
                        <input type="checkbox" class="organisation-checkbox" name="organisation" value="<?php echo $ID; ?>" onclick="limitOrganisationSelection(this)">
                        <span><?php the_title(); ?></span>
                    </label>
                <?php endwhile; ?>
            </div>
        </div>
        <small class="organisation-hint">
            <?php _e("If you couldn't find your organisation, you can create one by checking the checkbox below.", 'weadapt'); ?>
        </small>
        <label for="add_org" class="registration__checkbox">
            <input type="checkbox" id="add_org" name="add_org" value="1" onclick="limitOrganisationSelection()">
            <span><?php _e('I want to add my own organization', 'weadapt'); ?></span>
        </label>
    </div>
</div>
</div>

<div class="popup__separator"></div>

<div class="register-profile__item">
	<div class="registration__checkbox">
		<label class="registration__checkbox">
			<input type="checkbox" name="AGREE_TO_TERMS" value="1" required="">
			<span class="checkbox-label">
				<?php $terms = get_field('terms-policies', 'options');
				echo wpautop(wp_kses_post($terms['description']), false); ?>
			</span>
		</label>
	</div>
</div>


<!-- <?php
		$google_recaptcha_site_key = get_field('google_recaptcha_site_key', 'options');

		if (!empty($google_recaptcha_site_key)) {
			echo sprintf('<div id="g-recaptcha" data-sitekey="%s"></div>', esc_attr($google_recaptcha_site_key));

		?>
			<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

			<script type="text/javascript">
				var onloadCallback = function() {
					grecaptcha.render('g-recaptcha', {
						'sitekey' : '<?php echo esc_attr($google_recaptcha_site_key); ?>'
					});
				};
			</script>
		<?php
		}
		?> -->

<?php wp_nonce_field('ajax-create-nonce', 'ajax_create_nonce'); ?>
<input type="hidden" name="redirect_to" value="<?php echo get_current_url(); ?>">
<input type="hidden" name="action" value="theme_ajax_create">

<div class="wp-block-buttons">
	<div class="wp-block-button">
		<button class="wp-block-button__link" type="submit"><?php esc_html_e('Create', 'weadapt'); ?></button>
	</div>
</div>
<script>
	function updateSelectedOptions() {
		const select = document.getElementById('user-roles');
		const selectedOptionsContainer = document.getElementById('selected-options');
		selectedOptionsContainer.innerHTML = '';

		Array.from(select.selectedOptions).forEach(option => {
			const selectedOption = document.createElement('div');
			selectedOption.classList.add('selected-option');
			selectedOption.textContent = option.text;

			const removeButton = document.createElement('span');
			removeButton.classList.add('remove-selected-option');
			removeButton.textContent = 'x';
			removeButton.onclick = (event) => {
				event.stopPropagation(); // Prevent the event from bubbling up to parent elements
				option.selected = false;
				updateSelectedOptions();
			};

			selectedOption.appendChild(removeButton);
			selectedOptionsContainer.appendChild(selectedOption);
		});
	}

	document.addEventListener('DOMContentLoaded', () => {
		updateSelectedOptions();
		document.getElementById('user-roles').addEventListener('change', updateSelectedOptions);
	});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    updateSelectedOptions();
    document.getElementById('user-roles').addEventListener('change', updateSelectedOptions);

    document.getElementById('add_org').addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.organisation-checkbox').forEach(function(checkbox) {
                checkbox.checked = false;
                checkbox.closest('.organisation-item').classList.remove('selected');
            });
        }
    });

    document.querySelectorAll('.organisation-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('click', function() {
            document.getElementById('add_org').checked = false;

            document.querySelectorAll('.organisation-checkbox').forEach(function(otherCheckbox) {
                if (otherCheckbox !== checkbox) {
                    otherCheckbox.checked = false;
                    otherCheckbox.closest('.organisation-item').classList.remove('selected');
                }
            });

            if (checkbox.checked) {
                checkbox.closest('.organisation-item').classList.add('selected');
            } else {
                checkbox.closest('.organisation-item').classList.remove('selected');
            }
        });
    });
});

function updateSelectedOptions() {
    const select = document.getElementById('user-roles');
    const selectedOptionsContainer = document.getElementById('selected-options');
    selectedOptionsContainer.innerHTML = '';

    Array.from(select.selectedOptions).forEach(option => {
        const selectedOption = document.createElement('div');
        selectedOption.classList.add('selected-option');
        selectedOption.textContent = option.text;

        const removeButton = document.createElement('span');
        removeButton.classList.add('remove-selected-option');
        removeButton.textContent = 'x';
        removeButton.onclick = (event) => {
            event.stopPropagation();
            option.selected = false;
            updateSelectedOptions();
        };

        selectedOption.appendChild(removeButton);
        selectedOptionsContainer.appendChild(selectedOption);
    });
}

function validateForm(event) {
    var organisationChecked = document.querySelector('.organisation-checkbox:checked');
    var addOrgChecked = document.getElementById('add_org').checked;
    if (!organisationChecked && !addOrgChecked) {
        alert("Please select an organisation or check the 'add my own organisation' checkbox.");
        event.preventDefault();
        return false;
    }
    
    if (organisationChecked && addOrgChecked) {
        alert("Please select only one option: either choose an organisation or check the 'add my own organisation' checkbox.");
        event.preventDefault();
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('form').addEventListener('submit', validateForm);
});

function filterOrganisations() {
    var input = document.getElementById('organisation-search');
    var filter = input.value.toLowerCase();
    var items = document.querySelectorAll('#organisation-list .organisation-item');
    items.forEach(function(item) {
        var text = item.textContent || item.innerText;
        if (text.toLowerCase().indexOf(filter) > -1) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>