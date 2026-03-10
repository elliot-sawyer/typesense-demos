#!/bin/bash

vendor/bin/sake-release live production
vendor/bin/sake dev/tasks/Firesphere-CSPHeaders-Tasks-SRIRefreshTask
