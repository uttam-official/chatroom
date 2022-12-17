<section id="login" class="row w-100 h-100 justify-content-center align-item-center">
    <div class="col-md-6 col-10 my-5">
        <?php if ($this->session->flashdata('login_err')) : ?>
        <div class="my-2 px-5 py-2 bg-danger">
            <?= $this->session->flashdata('login_err') ?>
        </div>
        <?php endif; ?>
        <?= form_open('validate') ?>
        <div class="mb-3">
            <label for="" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Password</label>
            <input type="password" class="form-control" name="pass">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="">
            <label class="form-check-label" for="exampleCheck1">Check me out</label>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div class="help-text">
            Not registered! <a class="text-primary" href="/register">Please Register</a>
        </div>
    </div>

</section>