<div>
    <ul>
        @foreach ($getRecord()->equipment as $equip)
            <li>{{$equip->category}} - {{ $equip->brand_name }} - {{ $equip->model }} ({{ $equip->serial_number }})</li>
        @endforeach
    </ul>
</div>