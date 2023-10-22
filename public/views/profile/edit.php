<div class="sub-title">
    <h2>Edit Profile</h2>
</div>

<div class="content-page pt-2">
    <div class="ps-4 pe-4">
        <form method="post">
            <div class="row">
                <input type="hidden" name="username" value="<?= $user->get('user_login') ?>" />

                <div class="form-group col-md-6">
                    <label for="username-field">Username</label>
                    <input type="text" class="form-control" id="username-field" placeholder="Username" value="<?= $user->get('user_login') ?>" disabled>
                </div>

                <div class="form-group col-md-6">
                    <label for="email-field">Email</label>
                    <input type="email" name="email" class="form-control" id="email-field" placeholder="Email" value="<?= $user->get('user_email') ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="name-field">Full Name</label>
                    <input type="text" name="name" class="form-control" id="name-field" placeholder="Enter your full name" value="<?= $user->get('display_name') ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="name-field">Nickname</label>
                    <input type="text" name="nickname" class="form-control" id="name-field" placeholder="Enter your nickname" value="<?= $user->get('user_nicename') ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="country-field">Country</label>
                    <select name="country" id="country-field" class="form-control">
                        <option value="" selected>Choose...</option>
                        <?php foreach ($countries as $country) : ?>
                            <option value="<?= $country['name'] ?>" <?= $user->get('country') == $country['name'] ? 'selected' : '' ?>><?= $country['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="profession_title-field">Profession Title</label>
                    <input type="text" name="profession_title" class="form-control" id="profession_title-field" placeholder="Eg.: Software Engineer" value="<?= $user->get('profession_title') ?>">
                </div>

                <div class="form-group col-md-12">
                    <label for="skills-field">Skills</label>
                    <input type="text" name="skills" class="form-control" id="skills-field" placeholder="Enter skills seperated by comma (,)" value="<?= $user->get('skills') ?>">
                </div>

                <div class="form-group col-md-12">
                    <label for="languages-field">Languages</label>
                    <input type="text" name="languages" class="form-control" id="languages-field" placeholder="Enter languages seperated by comma (,)" value="<?= $user->get('languages') ?>">
                </div>

                <div class="form-group col-md-12">
                    <label for="about-field">About (300 characters)</label>
                    <textarea name="about" maxlength="300" class="form-control" id="about-field" placeholder="Write short description about yourself" rows="4"><?= $user->get('about') ?></textarea>
                </div>

                <div class="form-group col-md-12">
                    <label for="name-field">Professional Description</label>
                    <textarea id="editor" name="professional_description"><?= $user->get('professional_description') ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?= str_replace('/views', '', plugin_dir_url(__DIR__)) . 'js/ckeditor.js' ?>"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>