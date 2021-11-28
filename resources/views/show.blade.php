@extends('reviz::layout')

@section('content')

  <div class="w-1/2">
    <h1 class="text-xl font-semibold mb-6">Changes Details</h1>
  
    <div class="flex flex-col space-y-5 mb-10">
      <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
          <img class="h-10 w-10 rounded-full" src="{{$revision->getUserGravatar()}}">
        </div>
        <div class="ml-4">
          <div class="font-medium text-gray-900">
            {{$revision->getUserName()}}
          </div>
          <div class="text-gray-500">
            {{$revision->getUserEmail()}}
          </div>
        </div>
      </div>
  
      <div>
        Made changes at:
        <span class="text-gray-600">{{$revision->created_at_formatted}}</span>
      </div>
    </div>
  
    <div>
      <h4 class="text-lg mb-2 font-semibold">Changes Detail</h4>
  
      <div class="bg-gray-100 p-2 border rounded">
        <p class="text-sm font-mono">table: {{$revision->revizable_type}}</p>
        <p class="text-sm font-mono">id: {{$revision->revizable_id}}</p>
      </div>
  
      @foreach ($revision->old_value as $field => $value)
        <div data-diff
          data-field="{{ $field }}"
          data-old-value="{{ $value }}"
          data-new-value="{{ $revision->new_value[$field] }}"></div>
      @endforeach
  
      <div id="resultDiff"></div>
    </div>
  </div>

@endsection

@push('scripts')
  <script defer>
    let result = document.querySelector('#resultDiff')
    document.querySelectorAll('[data-diff]').forEach((item) => {
      let field = item.getAttribute('data-field')
      let oldValue = item.getAttribute('data-old-value')
      let newValue = item.getAttribute('data-new-value')

      let containerChanges = document.createElement('div')
      containerChanges.className = 'item-changes border-b py-5'
      
      let fieldName = document.createElement('div')
      fieldName.className = 'item-changes-field-name font-medium text-lg mb-1'
      fieldName.appendChild(document.createTextNode(field))

      containerChanges.appendChild(fieldName)

      // diffChars | diffWords | diffLines
      let obj = Diff.diffLines(oldValue, newValue)
      let fragment = document.createDocumentFragment();

      obj.forEach((part) => {
        // green for additions, red for deletions, grey for common parts
        // const color = part.added ? 'green' : part.removed ? 'red' : 'grey';
        // span = document.createElement('div');
        // span.style.color = color;
        // span.appendChild(document.createTextNode(part.value));


        span = document.createElement('div')
        span.className = 'p-2 bg-white'
        if (part.added) {
          span.className = 'p-2 bg-green-200'
        }
        if (part.removed) {
          span.className = 'p-2 bg-red-200'
        }
        span.appendChild(document.createTextNode(part.value))

        fragment.appendChild(span);
      })

      containerChanges.appendChild(fragment);
      document.querySelector('#resultDiff').appendChild(containerChanges)
    })
  </script>
@endpush