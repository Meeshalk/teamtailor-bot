<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-lock"></i> Change Password</h3>
  </div>

    <div class="box-body">
        <form class="form" method="POST" id="changePasswordForm" action="{{ route('profile.changePassword') }}">
            @csrf

            <div class="form-group">
                <label for="current-password">Current Password</label>
                <input id="current-password" type="password" class="form-control" name="current-password" required>
            </div>

            <div class="form-group">
                <label for="new-password">New Password</label>
                <input id="new-password" type="password" class="form-control" name="new-password" required>
            </div>

            <div class="form-group">
                <label for="new-password-confirm">Confirm New Password</label>
                <input id="new-password-confirm" type="password" class="form-control" name="new-password_confirmation" required>
            </div>

            <input type="submit" class="btn btn-success" value="Change Password">
        </form>
    </div>
  </div>
