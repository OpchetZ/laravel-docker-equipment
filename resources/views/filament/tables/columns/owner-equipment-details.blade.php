<div>
    <strong>คอมพิวเตอร์:</strong>
    <ul>
        @foreach ($getRecord()->pc as $computer)
            <li>{{ $computer->brand }} - {{ $computer->model }} ({{ $computer->service_tag }})</li>
        @endforeach
    </ul>

    <strong>จอ:</strong>
    <ul>
        @foreach ($getRecord()->monitor as $monitor)
            <li>{{ $monitor->monibrand }} - {{ $monitor->monimodel }} ({{ $monitor->serialnum	 }})</li>
        @endforeach
    </ul>
</div>
