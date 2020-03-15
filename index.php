<?php

use Illuminate\Database\Capsule\Manager as Capsule;

require_once './bootstrap.php';

$projects = Capsule::table('project')
	->select([
		'project.*',
		'employer.login AS employer_login',
		'employer.first_name AS employer_first_name',
		'employer.last_name AS employer_last_name'
	])
	->join('employer', 'employer.id', '=', 'employer_id')
    ->get();

$skillStatistic = Capsule::table('project_skill')
     ->select([
	     Capsule::raw('COUNT(project_id) AS project_count'),
         'skill.name AS skill_name'
     ])
     ->join('skill', 'skill.id', '=', 'skill_id')
	 ->groupBy('skill_id')
	 ->get();

$budgetStatistic = Capsule::table('project')
  ->select([
	  Capsule::raw('COUNT(id) AS project_count'),
	  Capsule::raw("CASE
	      WHEN budget < 500 OR budget IS NULL THEN 'until_500'
	      WHEN budget >= 500 AND budget < 1000 THEN 'until_1000'
	      WHEN budget >= 100 AND budget < 5000 THEN 'until_5000'
	      WHEN budget >= 5000 AND budget < 10000 THEN 'until_10000'
	      WHEN budget >= 10000 THEN 'more_10000'
	  END AS type")
  ])
  ->groupBy('type')
  ->get();

?>

<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

	<title>Hello, world!</title>
</head>
<body>

<div class="container">
	<h2>Проекты</h2>
	<table id="project-list-table" class="table">
		<thead>
			<tr>
				<th>Проект</th>
				<th>Бюджет</th>
				<th>Заказчик</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($projects as $project): ?>
				<tr>
					<td><a href="https://freelancehunt.com/project<?= $project->uri ?>"><?= $project->name ?></a></td>
					<td><?= $project->budget ?></td>
					<td><?= $project->employer_first_name . ' ' . $project->employer_last_name ?> [<?= $project->employer_login ?>]</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<div class="container">
	<h2>Статистика навыков</h2>
	<table id="skill-statistic-table" class="table">
		<thead>
			<tr>
				<th>Навык</th>
				<th>Кол-во проектов</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($skillStatistic as $statistic): ?>
			<tr>
				<td><?= $statistic->skill_name ?></td>
				<td><?= $statistic->project_count ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<div class="container">
	<h2>Статистика по бюджету проектов</h2>
	<canvas id="pie-chart" style="width:300px;height:150px;"></canvas>
</div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<script type="text/javascript">

$(document).ready(function () {
    $('#project-list-table').DataTable();
    $('#skill-statistic-table').DataTable();

    new Chart(document.getElementById("pie-chart"), {
        type: 'pie',
        data: {
            labels: ["до 500 грн", "500-1000 грн", "1000-5000 грн", "5000-10000 грн", "более 10000 грн"],
            datasets: [{
                backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
                data: [
                    <?= $budgetStatistic->where('type', 'until_500')->first()->project_count ?? 0 ?>,
	                <?= $budgetStatistic->where('type', 'until_1000')->first()->project_count ?? 0 ?>,
	                <?= $budgetStatistic->where('type', 'until_5000')->first()->project_count ?? 0 ?>,
	                <?= $budgetStatistic->where('type', 'until_1000')->first()->project_count ?? 0 ?>,
	                <?= $budgetStatistic->where('type', 'more_1000')->first()->project_count ?? 0 ?>
                ]
            }]
        },
    });
} );

</script>

</body>
</html>