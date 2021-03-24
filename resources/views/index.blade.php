@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="col-6 mx-auto">
            <nav class="nav nav-tabs nav-stacked">
                <a class="nav-link active" href="#">Home</a>
                <a class="nav-link" href="/settings">Settings</a>
            </nav>
            <div class="text-center mt-3 mb-3">
                <h1>Currency converter</h1>
            </div>
            <form method="POST" action="/">
                @csrf

                <div class="form-group w-75">
                    <label for="input">Input</label>
                    <input type="text" name="input" id="input" class="form-control" value='{{ old('input', '') }}'>
                    @error('input')
                        <div class="mt-2 text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="row justify-content-center align-items-center">
                    <div class="col-5">
                        <div class="form-group pt-3">
                            <label for="from">From</label>
                            <select class="form-control flex-fill" name="from" id="from">
                                @if ($currencies->count())
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->short_name }}"
                                            {{ old('from') == $currency->short_name ? 'selected' : '' }} @if ($from) {{ $currency->short_name == $from ? 'selected' : '' }} @endif>{{ $currency->full_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        @error('from')
                            <div class="mt-2 text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-2 text-center mt-3">
                        <a href="#" onclick="reverse()" class="badge badge-secondary">Reverse</a>
                    </div>
                    <div class="col-5">
                        <div class="form-group pt-3">
                            <label for="to">To</label>
                            <select class="form-control flex-fill" name="to" id="to">
                                @if ($currencies->count())
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->short_name }}"
                                            {{ old('to') == $currency->short_name ? 'selected' : '' }} @if ($to) {{ $currency->short_name == $to ? 'selected' : '' }} @endif>{{ $currency->full_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 ml-3">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </form>
            <div class="text-center mt-3">
                <h1>Results shown here</h1>
            </div>
            @if ($results)
                <div class="text-center">
                    <h3>
                        Result
                    </h3>
                    <h4>
                        {{ $results['result'] }}
                    </h4>
                    <h3 class="mt-3">
                        Exchange rate
                    </h3>
                    <h4>
                        {{ $results['exchange_rate'] }}
                    </h4>
                </div>
            @endif
        </div>
    </div>
    <script>
        function reverse() {
            let from = document.getElementById('from');
            let to = document.getElementById('to');

            let currentFrom = JSON.parse(JSON.stringify(from.value));
            let currentTo = JSON.parse(JSON.stringify(to.value));

            from.value = currentTo;
            to.value = currentFrom;

            return false;
        }

    </script>
@endsection
