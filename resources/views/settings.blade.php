@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="col-6 mx-auto">
            <nav class="nav nav-tabs nav-stacked">
                <a class="nav-link" href="/">Home</a>
                <a class="nav-link active" href="#">Settings</a>
            </nav>
            <div class="text-center mt-3 mb-3">
                <h1>Settings</h1>
            </div>
            <div>
                <form method="POST" action="/settings">
                    @csrf
                    <div class="mt-3 mb-3">
                        <h2>Default exchange</h2>
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

                            @error('to')
                                <div class="mt-2 text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3 mb-3">
                        <h2>Visible currencies</h2>
                    </div>
                    @error('currencies')
                        <div class="mt-2 mb-2 text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <div>
                        @foreach ($currencies as $currency)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="{{ $currency->short_name }}"
                                    name="currencies[]" value="{{ $currency->id }}" @if ($currency->hidden == false) checked @endif>
                                <label class="form-check-label">{{ $currency->full_name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <input class="btn btn-primary mt-3 ml-3" type="submit" value="Save">
                    </div>
                </form>
            </div>
            <div>
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
