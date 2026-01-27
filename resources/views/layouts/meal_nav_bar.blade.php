<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="dailyprocess">
       Meal Process <span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">

      <li><a class="dropdown-item" href="{{ route('mealtypes')}}">Meal Type</a></li>

      <li><a class="dropdown-item" href="{{ route('mealrequests')}}">Meal Requests</a></li>

      <li><a class="dropdown-item" href="{{ route('mealrecivedmark')}}">Meal Receiving Mark</a></li>

    </ul>
  </div>

  
   <a role="button" class="btn navbtncolor" href="{{ route('mealfinalaaprovel') }}" id="taskapprove">Meal Deduction Approve <span class="caret"></span></a>

    <a role="button" class="btn navbtncolor" href="{{ route('mealattendancededuction') }}" id="attendancetaskapprove">Meal Allowance Approve<span class="caret"></span></a>
 
</div>


