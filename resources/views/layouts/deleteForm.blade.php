<form class="inline" action="{{ route($form['route'], $form['id']) }}" method="post" data-msg="{{ $form['msg'] }}">
  <input type="submit" class="btn btn-xs btn-danger ajax-delete mb5i" value="Delete">
    @method('delete')
    @csrf
</form>
