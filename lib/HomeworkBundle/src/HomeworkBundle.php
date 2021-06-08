<?php

namespace SymfonySkillbox\HomeworkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use SymfonySkillbox\HomeworkBundle\DependencyInjection\HomeworkBundleExtension;

class HomeworkBundle extends Bundle
{

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new HomeworkBundleExtension();
        }

        return $this->extension;
    }

}
