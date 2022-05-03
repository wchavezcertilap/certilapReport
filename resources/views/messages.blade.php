
@if (count($errors) > 0)
<div class="box-body">
  <div class="callout callout-danger">
    <h4>Error!</h4>

    <p><h3>Corrige los siguientes errores.</h3></p>
     <ul>
            @foreach ($errors->all() as $message)
                <h4> <li>{{ $message }}</li> </h4>
            @endforeach
        </ul>
  </div>
</div>
@endif