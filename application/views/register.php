<section id="register" class="row w-100 h-100 justify-content-center align-item-center">
    <div class="col-md-6 col-10 my-5">
        <?php if ($this->session->flashdata('form_err')) : ?>
        <div class="my-2 px-5 py-2 bg-danger">
            <?= $this->session->flashdata('form_err') ?>
        </div>
        <?php endif; ?>
        <?= form_open('register_user') ?>
        <div class="mb-3">
            <label for="" class="form-label">Name</label>
            <input type="text" class="form-control" name="name">
            <?= form_error('name') ?>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Email address</label>
            <input type="text" class="form-control" name="email" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Password</label>
            <input type="password" class="form-control" name="pass">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="con_pass">
        </div>
        <button type="register" class="btn btn-primary">Submit</button>
        </form>
        <div class="help-text">
            Already registered! <a class="text-primary" href="/login">Please Login</a>
        </div>
    </div>
</section>