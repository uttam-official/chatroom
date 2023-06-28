<div class="user-modal">
    <?php if ($this->session->flashdata('form_err')) : ?>
        <div class="my-2 px-5 py-2 bg-danger">
            <?= $this->session->flashdata('form_err') ?>
        </div>
    <?php endif; ?>
    <div class="user-modal-container">
        <ul class="switcher">
            <li><a href="#0" id="loginTab">Sign in</a></li>
            <li><a href="#0" id="registerTab">New account</a></li>
        </ul>

        <div id="login">
            <?= form_open('validate', ['class' => 'form']) ?>
            <p class="fieldset">
                <label class="image-replace email" for="signin-email">E-mail</label>
                <input class="full-width has-padding has-border" name="email" id="signin-email" type="email" placeholder="E-mail">
                <span class="error-message">An account with this email address does not exist!</span>
            </p>

            <p class="fieldset">
                <label class="image-replace password" for="signin-password">Password</label>
                <input class="full-width has-padding has-border" id="signin-password" type="password" placeholder="Password" name="pass">
                <a href="#0" class="hide-password">Show</a>
                <span class="error-message">Wrong password! Try again.</span>
            </p>

            <p class="fieldset">
                <input type="checkbox" id="remember-me" checked>
                <label for="remember-me">Remember me</label>
            </p>

            <p class="fieldset">
                <input class="full-width" type="submit" value="Login">
            </p>
            </form>

            <p class="form-bottom-message"><a href="#0">Forgot your password?</a></p>
            <!-- <a href="#0" class="close-form">Close</a> -->
        </div>

        <div id="signup">
            <?= form_open('register_user', ['class' => 'form']) ?>
            <p class="fieldset">
                <label class="image-replace username" for="signup-username">Fullname</label>
                <input class="full-width has-padding has-border" id="signup-username" type="text" placeholder="Fullname" name="name">
                <span class="error-message">Your username can only contain numeric and alphabetic
                    symbols!</span>
            </p>

            <p class="fieldset">
                <label class="image-replace email" for="signup-email">E-mail</label>
                <input class="full-width has-padding has-border" id="signup-email" name="email" type="email" placeholder="E-mail">
                <span class="error-message">Enter a valid email address!</span>
            </p>

            <p class="fieldset">
                <label class="image-replace password" for="signup-password">Password</label>
                <input class="full-width has-padding has-border" name="pass" id="signup-password" type="password" placeholder="Password">
                <a href="#0" class="hide-password">Show</a>
                <span class="error-message">Your password has to be at least 6 characters long!</span>
            </p>
            <p class="fieldset">
                <label class="image-replace password" for="signup-password">Confirm Password</label>
                <input class="full-width has-padding has-border" id="signup-password" type="password" placeholder="Confirm Password" name="con_pass">
                <a href="#0" class="hide-password">Show</a>
                <span class="error-message">Your password has to be at least 6 characters long!</span>
            </p>

            <p class="fieldset">
                <input type="checkbox" id="accept-terms">
                <label for="accept-terms">I agree to the <a class="accept-terms" href="#0">Terms</a></label>
            </p>

            <p class="fieldset">
                <input class="full-width has-padding" type="submit" value="Create account">
            </p>
            </form>

            <!-- <a href="#0" class="cd-close-form">Close</a> -->
        </div>

        <div id="reset-password">
            <p class="form-message">Lost your password? Please enter your email address.</br> You will receive a
                link to create a new password.</p>

            <form class="form">
                <p class="fieldset">
                    <label class="image-replace email" for="reset-email">E-mail</label>
                    <input class="full-width has-padding has-border" id="reset-email" type="email" placeholder="E-mail">
                    <span class="error-message">An account with this email does not exist!</span>
                </p>

                <p class="fieldset">
                    <input class="full-width has-padding" type="submit" value="Reset password">
                </p>
            </form>

            <p class="form-bottom-message"><a href="#0">Back to log-in</a></p>
        </div>
        <a href="#0" class="close-form">Close</a>
    </div>
</div>