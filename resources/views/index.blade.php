@extends('reviz::layout')

@section('content')

<div class="flex flex-col">
  <div class="overflow-x-auto sm:-mx-6 lg:-mx-8 mb-4 lg:mb-10">
    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
      <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ID
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Author
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Object
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Changes
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Action</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($revisions as $item)
              <tr>
                <td class="px-6 py-4">
                  {{$item->id}}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img class="h-10 w-10 rounded-full" src="{{$item->getUserGravatar()}}">
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{$item->getUserName()}}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{$item->getUserEmail()}}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="bg-gray-100 p-2 border rounded">
                    <p class="text-sm font-mono">table: {{$item->revizable_type}}</p>
                    <p class="text-sm font-mono">id: {{$item->revizable_id}}</p>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <a href="{{route('revizPanel.show', $item->id)}}" class="italic text-blue-500 hover:underline text-sm hover:text-blue-400">
                    {{$item->count_updated_fields}} Fields
                  </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-xs text-gray-600">{{$item->created_at_formatted}}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <a href="{{route('revizPanel.show', $item->id)}}" class="text-white hover:text-gray-100 bg-blue-500 hover:bg-blue-400 px-4 py-2 rounded">See Changes</a>
                </td>
              </tr>
            @empty
                <tr>
                  <td colspan="100%">
                    <div class="py-3 text-center text-gray-600">
                      No Data
                    </div>
                  </td>
                </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {!! $revisions->links('reviz::pagination', [
    'paginator' => $revisions
  ]) !!}
</div>


@endsection