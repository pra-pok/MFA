<form action="{{route($_base_route . '.destroy',['id' => $item->id])}}" class="d-inline" method="post"
      onsubmit="return confirm('are you sure to delete?')">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit" class="btn rounded-pill btn-danger" title="Move to Trash"><i class="bx bx-trash me-1"></i></button>
</form>


