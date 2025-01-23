{{--edit-social media--}}
<form action="{{ route('organization-social-media.update', $data['record']->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="organization_id" value="{{ $data['record']->id ?? '' }}"/>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th width="50">SN</th>
            <th width="300">Social Media</th>
            <th>Link</th>
        </tr>
        </thead>
        <tbody class="table-border-bottom-0">
        @foreach($data['social'] as $key => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <i class="icon-base {{ $item['icon'] }} icon-sm"></i>
                    {{ $item['name'] }}
                    <input type="hidden" name="name[]" value="{{ $item['name'] }}" />
                </td>
                <td>
                    <input type="text" name="url[]" class="form-control" value="{{ $item['url'] ?? '' }}"/>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</form>
