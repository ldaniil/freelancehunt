<?php

use Src\FreelancehuntApi;
use Illuminate\Database\Capsule\Manager as Capsule;

require_once dirname(__DIR__) . '/bootstrap.php';

$api = new FreelancehuntApi($_ENV['FREELANCEHUNT_API_TOKEN']);
$page = 1;

while (true) {
	$projects = $api->getProjectsList($page);

	$insertEmployers = [];
	$insertEmployerIds = [];
	$insertSkills = [];
	$insertSkillId = [];
	$insertProjects = [];
	$insertProjectSkills = [];

	if (!empty($projects['data'])) {
		foreach ($projects['data'] as $project) {
			$insertEmployerIds[] = $project['attributes']['employer']['id'];

			$insertEmployers[ $project['attributes']['employer']['id'] ] = [
				'id' => $project['attributes']['employer']['id'],
				'login' => $project['attributes']['employer']['login'],
				'first_name' => $project['attributes']['employer']['first_name'],
				'last_name' => $project['attributes']['employer']['last_name'],
			];

			$insertProjects[] = [
				'id' => $project['id'],
				'employer_id' => $project['attributes']['employer']['id'],
				'name' => $project['attributes']['name'],
				'budget' => $project['attributes']['budget']['amount'] ?? null,
				'uri' => str_replace('https://freelancehunt.com/project', '', $project['links']['self']['web'])
			];

			foreach ($project['attributes']['skills'] as $skill) {
				$insertSkillIds[] = $skill['id'];

				$insertSkills[ $skill['id'] ] = [
					'id' => $skill['id'],
					'name' => $skill['name']
				];

				$insertProjectSkills[] = [
					'project_id' => $project['id'],
					'skill_id' => $skill['id']
				];
			}
		}

		$existsEmployerIds = Capsule::table('employer')
		                            ->select('id')
		                            ->whereIn('id', $insertEmployerIds)
		                            ->pluck('id')
		                            ->toArray();

		if ($existsEmployerIds) {
			foreach ($existsEmployerIds as $existsEmployerId) {
				unset($insertEmployers[$existsEmployerId]);
			}
		}

		$existsSkillIds = Capsule::table('skill')
		                         ->select('id')
		                         ->whereIn('id', $insertSkillIds)
		                         ->pluck('id')
		                         ->toArray();

		if ($existsSkillIds) {
			foreach ($existsSkillIds as $existsSkillId) {
				unset($insertSkills[$existsSkillId]);
			}
		}

		Capsule::table('employer')->insert($insertEmployers);
		Capsule::table('project')->insert($insertProjects);
		Capsule::table('skill')->insert($insertSkills);
		Capsule::table('project_skill')->insert($insertProjectSkills);
	}

	if ($page === 5) {
		break;
	}

	$page++;
}




