<x-modal id="addCustomerModal" title="Naya Customer Add Karein" icon="bi-person-plus-fill">
    <form id="addCustomerForm" method="POST" action="{{ route('customers.store') }}">
        @csrf
        @include('customers._fields', ['customer' => null])
    </form>

    <x-slot:footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="addCustomerForm" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Customer</button>
    </x-slot:footer>
</x-modal>
