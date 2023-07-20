@php
    $months = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
@endphp
<x-header/>
<x-app-layout/>

@php
    if(isset($budgetId)) {
        $currentBudget = \App\Models\PlanBudget::whereId($budgetId)->first();
        $currentBudgetItems = json_decode(\App\Models\PlanBudget::whereId($budgetId)->pluck('dataset')->first(), true);
        $currentBudgetIncomes = json_decode(\App\Models\PlanBudget::whereId($budgetId)->pluck('incomes')->first(), true);

        usort($currentBudgetItems, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

    }
@endphp

<div>
    <div class="d-flex flex-row justify-content-center">
        <h2>Планирование бюджета</h2>
    </div>
    <div class="d-flex flex-row justify-content-center">
        <div class="d-flex flex-column mx-2 my-2 align-items-center">
            <div>

            </div>
            <h4>Список бюджетов</h4>
            <div class="d-flex flex-row justify-content-center">
                <table class="table table-bordered table-striped table-hover table-sm caption-top align-top">
                    <thead class="table-light text-center align-top">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Месяц</th>
                        <th scope="col">Год</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <tr>
                        <form class="" name="" id="category" method="post" enctype="multipart/form-data" action="{{route('planbudget.add')}}">
                            @csrf
                            <td>
                            </td>
                            <td>
                                <select name="month" class="form-select form-select-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-lg">
                                    @foreach($months as $key => $month)
                                        <option value="{{$key + 1}}">{{$month}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <x-text-input id="year" class="form-control form-control-sm block mt-1 items-center justify-center" type="text" name="year" required autofocus autocomplete="year" />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </td>
                            <td class="col-1 align-top">
                                <button type="submit" class="btn btn-success btn-sm" name="id" value="">Создать</button>
                            </td>
                            <td>

                            </td>
                        </form>
                    </tr>
                    @foreach (\App\Models\PlanBudget::where('user_id', Auth::user()->id)->orderBy('year')->orderBy('month')->get() as $key => $planBudget)

                        <tr>
                            <form class="" name="" id="category" method="post" enctype="multipart/form-data" action="{{route('planbudget.edit')}}">
                                @csrf
                                <input type="hidden" name="budgetId" value="{{$planBudget->id}}">
                                <td>
                                    {{$key + 1}}
                                </td>
                                <td>
                                    {{$months[$planBudget->month - 1]}}
                                </td>
                                <td>
                                    {{$planBudget->year}}
                                </td>
                                <td class="col-1 align-top">
                                    <button type="submit" class="btn btn-danger btn-sm" name="delete" value="{{$planBudget->id}}">Удалить</button>
                                </td>
                                <td class="col-1 align-top">
                                    <button type="submit" class="btn btn-primary btn-sm" name="budgetId" value="{{$planBudget->id}}">Планировать</button>
                                </td>
                            </form>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        <div class="d-flex flex-column mx-2 my-2 align-items-center">

            <div class="d-flex flex-row text-center fw-bold">
                @if (!isset($budgetId))
                    Текущий бюджет не выбран
                @else
                    Текущий бюджет -> {{$months[$currentBudget['month'] - 1]}} - {{$currentBudget['year']}}
                @endif
            </div>
            <div>
                @if (isset($budgetId))
                    <table class="table table-bordered table-striped table-hover table-sm align-top">
                        <thead>
                        <tr>
                            <th scope="col">Порядковый номер</th>
                            <th scope="col">Статья</th>
                            <th scope="col">Сумма</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <form id="category" method="post" enctype="multipart/form-data" action="{{route('planbudget.addItem')}}">
                                @csrf
                                <td class="text-center">
                                    <x-text-input id="order" class="block mt-1 w-50 items-center justify-center form-control form-control-sm" type="text" name="order" required autofocus autocomplete="order" />
                                    <x-input-error :messages="$errors->get('order')" class="mt-2" />
                                </td>
                                <td class="text-center justify-center">
                                    <select name="account_id" class="form-select form-select-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-lg">
                                        @foreach(\App\Models\Account::where('user_id', Auth::user()->id)->where('category', '!=', 0)->get() as $account)
                                            <option value="{{$account->id}}">{{$account->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <x-text-input id="sum" class="block mt-1 items-center justify-center form-control form-control-sm" type="number" name="sum" required autofocus autocomplete="sum" />
                                    <x-input-error :messages="$errors->get('sum')" class="mt-2" />
                                </td>
                                <td class="col-1 align-top">
                                    <button type="submit" class="btn btn-success btn-sm" name="currentBudget" value="{{$currentBudget->id}}">Добавить</button>
                                </td>
                            </form>
                        </tr>
                        @foreach($currentBudgetItems as $item)
                            <tr>
                                <form id="category" method="post" enctype="multipart/form-data" action="{{route('planbudget.deleteItem')}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$item['order']}}">
                                    <td  class="text-center">
                                        {{$item['order']}}
                                    </td>
                                    <td>
                                        {{\App\Models\Account::find($item['account'])->name}}
                                    </td>
                                    <td  class="text-center">
                                        {{$item['sum']}} р.
                                    </td>
                                    <td class="col-1 align-top text-center">
                                        <button type="submit" class="btn btn-danger btn-sm" name="currentBudget" value="{{$currentBudget->id}}">Удалить</button>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                @endif
            </div>
        </div>
        <div class="d-flex flex-column mx-2 my-2 align-items-center">
            <div class="d-flex flex-row text-center fw-bold">
                @if (isset($budgetId))
                    Планируемые доходы
                @endif
            </div>
            <div>
                @if (isset($budgetId))
                    <table class="table table-bordered table-striped table-hover table-sm align-top">
                        <thead>
                        <tr>
                            <th scope="col">Статья</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <form id="category" method="post" enctype="multipart/form-data" action="{{route('planbudget.addIncome')}}">
                                @csrf
                                <td class="text-center justify-center">
                                    <select name="account_id" class="form-select form-select-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-lg">
                                        @foreach(\App\Models\Account::where('user_id', Auth::user()->id)->where('category', '!=', 0)->get() as $account)
                                            <option value="{{$account->id}}">{{$account->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-1 align-top">
                                    <button type="submit" class="btn btn-success btn-sm" name="currentBudget" value="{{$currentBudget->id}}">Добавить</button>
                                </td>
                            </form>
                        </tr>
                        @foreach($currentBudgetIncomes as $item)
                            <tr>
                                <form id="category" method="post" enctype="multipart/form-data" action="{{route('planbudget.deleteIncome')}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$item['account']}}">
                                    <td>
                                        {{\App\Models\Account::find($item['account'])->name}}
                                    </td>
                                    <td class="col-1 align-top text-center">
                                        <button type="submit" class="btn btn-danger btn-sm" name="currentBudget" value="{{$currentBudget->id}}">Удалить</button>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        <div class="d-flex flex-column mx-2 my-2 align-items-center">
            @if (isset($budgetId))
                <form method="post" enctype="multipart/form-data" action="{{route('planbudget.addDescription')}}">
                    @csrf
                    <div>
                        <h4>Заметки бюджета</h4>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label"></label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="10" name="description">{{$currentBudget->description}}
                        </textarea>
                    </div>
                    <div class="col-1 align-top align-items-center text-center">
                        <button type="submit" class="btn btn-primary btn-sm  align-items-center text-center" name="currentBudget" value="{{$currentBudget->id}}">Сохранить</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
