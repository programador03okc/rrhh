@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div id="gantt_here" style="width:100%; height:auto; min-height: 300px;"></div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/cronoint.js')}}"></script>
@include('layout.fin_html')