<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 03-03-2019
 * Time: 21:41
 */
global $CONF, $OUTPUT;
$OUTPUT->header();
echo '
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>
            <form action="' .  $CONF->URLROOT . '/users/login" method="post">
                <div class="form-group">
                    <label>Email:<sup>*</sup></label>
                    <input type="text" name="email" class="form-control form-control-lg" value="' . $data['email'] . '">
                    <span class="invalid-feedback">' . $data['email_err'] .'</span>
                </div>
                <div class="form-group">
                    <label>Password:<sup>*</sup></label>
                    <input type="password" name="password" class="form-control form-control-lg" value="' . $data['password'] .'">
                    <span class="invalid-feedback">'. $data['password_err'] .'</span>
                </div>
                <div class="form-row">
                    <div class="col">
                        <input type="submit" class="btn btn-success btn-block" value="Login">
                    </div>
                    <div class="col">
                        <a href="' . $CONF->URLROOT .'/users/register" class="btn btn-light btn-block">No account? Register</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>';
$OUTPUT->footer();
