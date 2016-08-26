@extends('master.dashboard')

@section('content')
    <form method="post" attr-validate="true">
        <div class="activate-form">
            <div class="mb15">
                <input class="input-control" type="number" name="card_no" placeholder="First Citizen Card Number" attr-id="card_no" attr-validator="required" />
                <div class="validation-message" attr-validation-alertfor="card_no">
                    <div class="error"> Required field</div>
                </div>
            </div>
            <div class="mb15">
                <input class="input-control" type="text" name="name" placeholder="Full name" attr-id="name" attr-validator="required" />
                <div class="validation-message" attr-validation-alertfor="name">
                    <div class="error"> Required field</div>
                </div>
            </div>
            <div class="mb15">
                <input class="input-control" type="email" name="email" placeholder="Email" attr-id="email" attr-validator="required email" />
                <div class="validation-message" attr-validation-alertfor="email">
                    <div class="error"> Required field</div>
                    <div class="error email"> Invalid format</div>
                </div>
            </div>
            <div class="mb15">
                <input class="input-control" type="date" name="dob" placeholder="Date of Birth" attr-id="dob" attr-validator="required" />
                <div class="validation-message" attr-validation-alertfor="dob">
                    <div class="error"> Required field</div>
                </div>
            </div>
            <div class="c">
                <input class="btn-control" type="submit" value="Activate my First Citizen Card" />
            </div>
        </div>

    </form>
@endsection