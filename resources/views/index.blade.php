@extends('reviz::layout')

@section('content')

<div class="flex flex-col">
  <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
      <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Author
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Eloquent
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Funnel
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Batch
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
            @forelse ($rows as $item)
              <tr>
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
                  <div class="text-sm font-mono">table: posts</div>
                  <div class="text-sm font-mono">id: 9</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-mono font-semibold rounded-full bg-green-100 text-green-800">
                    Http
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  1
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm">Tue, 5 Oct 04.06</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" class="text-blue-600 hover:text-gray-100 bg-gray-200 hover:bg-gray-400 px-4 py-1 rounded">See Changes</a>
                  <a href="#" class="text-blue-600 hover:text-gray-100 bg-gray-200 hover:bg-gray-400 px-4 py-1 rounded">Rollback</a>
                </td>
              </tr>
            @empty
                <tr>
                  <td colspan="100%">No Data</td>
                </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


@endsection