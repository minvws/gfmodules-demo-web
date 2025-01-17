<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ConsentData;
use App\Dto\FlowState;
use App\Services\Uzi\UziAuthGuard;
use Illuminate\Contracts\Session\Session;

class FlowStateService
{
    public function __construct(
        protected UziAuthGuard $uziAuthGuard,
        protected Session $session,
    ) {
    }

    public function getFlowStateFromSession(): FlowState
    {
        $user = $this->uziAuthGuard->user();

        return new FlowState(user: $user, consentData: $this->getConsentFromSession());
    }

    public function setConsentDataInSession(ConsentData $state): void
    {
        $this->session->put('flow_consent_data', $state);
    }

    protected function getConsentFromSession(): ?ConsentData
    {
        return $this->session->get('flow_consent_data');
    }
}
