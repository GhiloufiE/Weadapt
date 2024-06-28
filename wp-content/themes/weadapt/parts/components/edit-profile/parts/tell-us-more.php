<?php
$user = !empty($args['user']) ? $args['user'] : null;

if (!empty($user)) :
	$user_ID = $user->ID;

	$interest_terms = get_terms([
		'taxonomy' => 'interest',
		'hide_empty' => false,
	]);

	$user_interest = !empty(get_field('interest', "user_$user_ID")) ? get_field('interest', "user_$user_ID") : [];
?>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var avatarInput = document.getElementById('account-avatar');
			var avatarPreview = document.getElementById('avatar-preview');
			var currentAvatar = document.querySelector('.edit-profile__avatar img');

			avatarInput.addEventListener('change', function() {
				var file = avatarInput.files[0];
				var reader = new FileReader();

				reader.onload = function(e) {
					avatarPreview.src = e.target.result;
					avatarPreview.style.display = 'block';
					currentAvatar.style.display = 'none';
				};

				if (file) {
					reader.readAsDataURL(file);
				}
			});
		});
	</script>
	<div class="edit-profile__item">
		<h4 class="edit-profile__title"><?php _e('Tell us more about you', 'weadapt'); ?></h4>

		<div class="row">
			<div class="col-12">
				<label for="account-avatar">
					<?php _e('Profile picture', 'weadapt'); ?>

					<div class="edit-profile__avatar">
						<?php echo get_avatar($user_ID, 98); ?>
						<img id="avatar-preview" src="<?php echo get_avatar_url($user_ID, ['size' => 98]); ?>" alt="Profile Picture" style="display:none;" width="98" height="98">

					</div>
				</label>

				<input id="account-avatar" class="edit-profile__avatar__input" type="file" name="avatar" accept=".png, .jpeg, .gif, .jpg" autocomplete="off">
				<span class="description"><?php _e('Files must be less than 256 MB. Allowed file types: png, gif, jpg, jpeg.', 'weadapt'); ?></span>
			</div>

			<div class="col-12">
				<label for="account-about"><?php _e('About me', 'weadapt'); ?></label>
				<textarea id="account-about" name="about_user"><?php echo get_user_excerpt($user_ID, -1); ?></textarea>
				<span class="description"><?php _e('Please provide a short description of yourself that you would like others to see (this can be edited later)', 'weadapt'); ?></span>
			</div>

			<div class="col-12">
				<label for="account-job-title"><?php _e('Job title', 'weadapt'); ?></label>
				<input type="text" id="account-job-title" name="job_title" value="<?php the_field('job_title', "user_$user_ID"); ?>">
				<span class="description"><?php _e('Please provide your Job Title. This will be displayed below your name around the site.', 'weadapt'); ?></span>
			</div>

			<?php if (!empty($interest_terms)) : ?>
				<div class="col-12">
					<label for="account-subjects-of-interest"><?php _e('Subjects of interest', 'weadapt'); ?></label>

					<div class="theme-select-wrap">
						<select multiple name="subjects-of-interest[]" id="account-subjects-of-interest">
							<?php foreach ($interest_terms as $term) :
								$is_selected = in_array($term->term_id, $user_interest) ? 'selected' : '';
							?>
								<option <?php echo $is_selected; ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
							<?php endforeach; ?>
						</select>

						<div class="theme-select"></div>
					</div>

					<span class="description"><?php _e('Start typing to choose your professional interests from existing tags. This helps us display content that is most relevant to you. Find people like yourself and content that interests you!', 'weadapt'); ?></span>
				</div>
			<?php endif; ?>

			<div class="col-12">
				<label for="account-referrer"><?php _e('Where did you hear about weADAPT?', 'weadapt'); ?></label>
				<input type="text" id="account-referrer" name="referrer" value="<?php the_field('referrer', "user_$user_ID"); ?>">
			</div>

			<div class="col-12">
				<label for="account-content-sought"><?php _e('What content are you hoping to find on weADAPT?', 'weadapt'); ?></label>
				<input type="text" id="account-content-sought" name="content_sought" value="<?php the_field('content_sought', "user_$user_ID"); ?>">
			</div>

			<div class="col-12">
				<label for="account-company"><?php _e('Company', 'weadapt'); ?></label>
				<input type="text" id="account-company" name="company" value="<?php the_field('company', "user_$user_ID"); ?>">
			</div>
		</div>
	</div>

<?php endif; ?>