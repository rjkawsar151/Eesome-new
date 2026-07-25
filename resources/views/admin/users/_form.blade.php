<div class="form-grid">
<div class="field"><label for="name">Name</label><input class="input" id="name" name="name" value="{{ old('name',$user->name ?? '') }}" required></div>
<div class="field"><label for="email">Email</label><input class="input" id="email" type="email" name="email" value="{{ old('email',$user->email ?? '') }}" required></div>
<div class="field"><label for="phone">Phone</label><input class="input" id="phone" name="phone" value="{{ old('phone',$user->phone ?? '') }}"></div>
<div class="field"><label for="role">Role</label><select class="select" id="role" name="role" required>@foreach(['customer','manager','admin','super admin'] as $role)<option value="{{ $role }}" @selected(old('role',$user->role ?? 'customer')===$role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
<div class="field"><label for="password">Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label><input class="input" id="password" type="password" name="password" @required(!isset($user))></div>
<div class="field"><label for="password_confirmation">Confirm password</label><input class="input" id="password_confirmation" type="password" name="password_confirmation" @required(!isset($user))></div>
</div>
<button class="btn btn-primary" style="margin-top:1.25rem">{{ isset($user) ? 'Save changes' : 'Create user' }}</button>
