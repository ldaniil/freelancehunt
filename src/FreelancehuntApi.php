<?php

namespace Src;

class FreelancehuntApi
{
	protected $api_token;

	protected $api_url = 'https://api.freelancehunt.com/v2';

	/**
	 * FreelancehuntApi constructor.
	 *
	 * @param string $api_token
	 */
	public function __construct(string $api_token)
	{
		$this->api_token = $api_token;
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @param string $type
	 * @return array
	 */
	public function query(string $method, array $params = [], string $type = 'GET'): array
	{
		$url = $this->api_url . '/' . $method;

		$queryParams = http_build_query($params);

		if ($type === 'GET') {
			$url .= '?' . $queryParams;
		}

		$authorization = 'Authorization: Bearer ' . $this->api_token;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		if ($type !== 'GET') {
			curl_setopt_array($curl, [
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $queryParams,
			]);
		}

		$data = json_decode(curl_exec($curl), true);

		curl_close($curl);

		return $data;
	}

	/**
	 * @param int $page
	 *
	 * @return array
	 */
	public function getProjectsList(int $page = 1): array
	{
		$params = [];

		if ($page > 1) {
			$params['page[number]'] = $page;
		}

		return $this->query('projects', $params);
	}
}