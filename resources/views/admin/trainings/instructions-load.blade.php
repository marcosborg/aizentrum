@foreach ($instructions as $instruction)
<li class="list-group-item list-group-item-action">
    <p class="instruction-text">{{ $instruction->text }}</p>
    <div class="d-flex justify-content-between">
        <button class="btn btn-outline-danger btn-sm" onclick="deleteInstruction({{ $instruction->id }})">Delete</button>
        <i class="fa-fw fas fa-arrows-alt"></i>
    </div>
</li>
@endforeach