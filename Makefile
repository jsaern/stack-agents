SHELL=/bin/bash
ACTIVE=Uncommon.php
SERVERNAME=stackr.test
WORK_DIR=~/Outputs/git/stack-agents-prv
STACK_DIR=/var/www/$(SERVERNAME)/vendor/nrwtaylor/stack-agent-thing/agents

.PHONY: help
help: ## Show this help
	@egrep -h '\s##\s' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

all:  publish check ## Do everything, in order

publish: ## Save file to active stackj
	cp -v $(WORK_DIR)/$(ACTIVE) $(STACK_DIR)/$(ACTIVE)

check: $(STACK_DIR)/$(ACTIVE) ## Check contents in active stack
	diff $(WORK_DIR)/$(ACTIVE) $(STACK_DIR)/$(ACTIVE); exit 0

test: ## Test agent
	cd /var/www/$(SERVERNAME); ./agent $(ACTIVE)

clean: ## Remove agent from stack
	rm -vf $(STACK_DIR)/$(ACTIVE)
