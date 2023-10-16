<div class="page-body">
    <div class="sub-title">
        <h2>New Contract</h2>
    </div>

    <div class="content-page">
        <div class="ps-4">
            <div class="container">
                <form method="post">
                <?php wp_nonce_field(); ?>
                    <div class="row">
                        <div class="col-md-3 border-right">
                            <div class="d-flex flex-column align-items-center text-center p-3 py-3"><img class="rounded-circle" width="150px" src="<?= "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" alt="<?= $user->get('display_name') ?>"><span class="font-weight-bold"><?= $user->get('user_nicename') ?></span><span class="text-black-50"><?= $user->get('user_login') ?></span><span> </span></div>
                        </div>
                        <div class="col-md-9 border-right">
                            <div class="p-3 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="text-right">Contract Details</h4>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label class="labels">Title</label>
                                        <input name="title" type="text" class="form-control" placeholder="Enter title" value="">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="labels">Description</label>
                                        <textarea id="editor" name="description"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6"><label class="labels">Expected Deadline</label>
                                        <input type="date" name="expected_deadline" class="form-control" placeholder="Expected date" value="">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="labels">Budget</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select name="budget_type" class="form-control">
                                                    <option>Select Type</option>
                                                    <option value="fixed">Fixed</option>
                                                    <option value="estimated">Estimated</option>
                                                </select>
                                            </div>
                                            <input type="number" step="any" name="budget" class="form-control" placeholder="Enter your budget" value="">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button class="btn btn-primary profile-button">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
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