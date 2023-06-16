@extends('layout')

@section('content')
    <div class="form-wrapper">
        <form method="get" action="{{url('/')}}">
            <div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Date from</label>
                    <input type="text" id="title" name="fromDate" class="form-control" placeholder="YYYY-MM-DD" required="">
                </div>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    @if(!empty($pricesData))
        <div class="table-wrapper">
            <div class="table-header">
                <div class="date">Date/Nights</div>
                <div class="persons">P</div>
                @for ($i = 1; $i <= 21; $i++)
                    <div class="nights">{{ $i }}</div>
                @endfor
            </div>
            <div class="table-body">
                @foreach($pricesData as $key => $dateData)
                    @foreach($dateData as $persons => $day)
                        <div class="date-item">
                            <span class="date">{{ $key }}</span>
                            <span class="persons">{{ $persons }}</span>
                            @foreach($day as $price)
                                <span class="nights">{{ $price }}</span>
                            @endforeach
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif
@endsection
