<?php

class AutomationExtension extends Minz_Extension {

	private string $sendUrl = '';
	private string $feeds = '';
	private array $bodyFields = array('' => '');
	private string $requestType = 'POST';

	#[\Override]
	public function init(): void {
		Minz_View::appendStyle($this->getFileUrl('style.css', 'css'));

		$this->registerHook('entry_before_insert', array($this, 'sendRequest'));
		$this->registerTranslates();
	}

	public function sendRequest(FreshRSS_Entry $entry): FreshRSS_Entry {
		$this->loadConfigValues();

		if (!$this->feeds === '') {
			if (!in_array($entry->feedId(), explode(',', $this->feeds))) {
				return $entry;
			}
		}

		$requestData = array();
		$pattern = '/\$\{(\w+)\}/'; // Matches any word inside ${}

		foreach ($this->bodyFields as $field => $data) {
			$value = $data;

			if (preg_match_all($pattern, $data, $matched)){
				foreach ($matched[0] as $match) {
					$value = match ($match) {
						'${id}' => str_replace($match, $entry->id(), $value),
						'${guid}' => str_replace($match, $entry->guid(), $value),
						'${title}' => str_replace($match, $entry->title(), $value),
						'${author}' => str_replace($match, $entry->author(), $value),
						'${authors}' => str_replace($match, $entry->authors(), $value),
						'${content}' => str_replace($match, $entry->content(false), $value),
						'${link}' => str_replace($match, $entry->link(), $value),
						'${date}' => str_replace($match, $entry->date(), $value),
						'${machineReadableDate}' => str_replace($match, $entry->machineReadableDate(), $value),
						'${lastSeen}' => str_replace($match, $entry->lastSeen(), $value),
						'${dateAdded}' => str_replace($match, $entry->dateAdded(), $value),
						'${isRead}' => str_replace($match, $entry->isRead(), $value),
						'${isFavorite}' => str_replace($match, $entry->isFavorite(), $value),
						'${isUpdated}' => str_replace($match, $entry->isUpdated(), $value),
						'${feedId}' => str_replace($match, $entry->feedId(), $value),
						'${tags}' => str_replace($match, $entry->tags(true), $value),
						'${hash}' => str_replace($match, $entry->hash(), $value),
						default => $value,
					};
				}
			}

			$requestData[$field] = $value;
		}

		switch ($this->requestType) {
			case 'GET':
				$this->sendGetRequest($requestData);
				break;	
			// case 'PUT':
			// 	$this->sendPutRequest($requestData);
			// 	break;
			// case 'DELETE':
			// 	$this->sendDeleteRequest($requestData);
			// 	break;
			// 	case 'HEAD':
			// 	$this->sendHeadRequest($requestData);
			// 	break;
			default:
				$this->sendPostRequest($requestData);
		}


		return $entry;

	}

	/** @param array<string> $requestData */
	private function sendGetRequest($requestData): void {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->sendUrl . '?' . http_build_query($requestData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		curl_close($ch);
	}

	/** @param array<string> $requestData */
	private function sendPostRequest($requestData): void {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->sendUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

		curl_exec($ch);
		curl_close($ch);
	}

	// /** @param array<string> $requestData */
	// private function sendPutRequest($requestData): void {
	// 	$ch = curl_init();
	//
	// 	curl_setopt($ch, CURLOPT_URL, $this->sendUrl . '?' . http_build_query($requestData));
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	// 	curl_exec($ch);
	// 	curl_close($ch);
	// }
	//
	// /** @param array<string> $requestData */
	// private function sendDeleteRequest($requestData): void {
	// 	$ch = curl_init();
	//
	// 	curl_setopt($ch, CURLOPT_URL, $this->sendUrl . '?' . http_build_query($requestData));
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	// 	curl_exec($ch);
	// 	curl_close($ch);
	// }
	//
	// /** @param array<string> $requestData */
	// private function sendHeadRequest($requestData): void {
	// 	$ch = curl_init();
	//
	// 	curl_setopt($ch, CURLOPT_URL, $this->sendUrl . '?' . http_build_query($requestData));
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
	// 	curl_exec($ch);
	// 	curl_close($ch);
	// }

	public function loadConfigValues(): void {
		if (!class_exists('FreshRSS_Context', false) || !FreshRSS_Context::hasUserConf()) {
			return;
		}

		$sendUrl = FreshRSS_Context::userConf()->attributeString('sendUrl');
		if ($sendUrl !== null) {
			$this->sendUrl = $sendUrl;
		}

		$bodyFields = FreshRSS_Context::userConf()->attributeArray('bodyFields');
		if ($bodyFields !== null) {
			$this->bodyFields = $bodyFields;
		}

		$requestType = FreshRSS_Context::userConf()->attributeString('requestType');
		if ($requestType !== null) {
			$this->requestType = $requestType;
		}

		$feeds = FreshRSS_Context::userConf()->attributeString('feeds');
		if ($feeds !== null) {
			$this->feeds = $feeds;
		}

	}

	#[\Override]
	public function handleConfigureAction(): void {
		$this->registerTranslates();

		if (Minz_Request::isPost()) {
			FreshRSS_Context::userConf()->_attribute('sendUrl', Minz_Request::paramString('send_url'));
			FreshRSS_Context::userConf()->_attribute('bodyFields', Minz_Request::paramArray('body_fields'));
			FreshRSS_Context::userConf()->_attribute('requestType', Minz_Request::paramString('request_type'));
			FreshRSS_Context::userConf()->_attribute('feeds', Minz_Request::paramString('feed_ids'));
			FreshRSS_Context::userConf()->save();
		}

		$this->loadConfigValues();
	}

	// getters for the config page
	public function getSendUrl(): string {
		return $this->sendUrl;
	}

	public function getBodyFields(): array {
		return $this->bodyFields;
	}

	public function getRequestType(): string {
		return $this->requestType;
	}

	public function getFeedIds(): string {
		return $this->feeds;
	}

}
