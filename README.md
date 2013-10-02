Ttree.SortableNode
==================

This package provide the logic needed for sorting node in TYPO3.TYPO3CR (TYPO3 Content Repository) by any even multiple
properties. The package support multiple sorting strategy (algorithm) and multile configure per NodeType and ParentNode
or ParentNode path.

This package is under heavy development, so everything can change, break ... or work as expected.

Configuration
=============

Configuration can be done in any Settings.yaml file:

	Ttree:
	  SortableNode:
		enabled: FALSE
		defaultSortingStrategy: DumbSortingStrategy
		configuration:
		  'Ttree.Plugin.MicroEvent:Event':
			10:
			  position:           'start'
			  label:              'Main sorting for Event node'
			  identifier:         '/sites/website/event'
			  strategy:           'DumbSortingStrategy'
			  properties:
				# eventStartDate:   ASC
				title:            ASC

The "identifier" can be a node path or a nodedata identifier (not the persistence_object_identifier). The "strategy"
can be configured, and you can even add your own strategy if needed. The property "Ttree.SortableNode.enabled" enable
automatic sorting of node based on an Aspect triggered after the update or add method from NodeDataRepository.

Currently only the DumbSortingStrategy (and inefficient) strategy is working. More powerful strategy will be added in the
future.

A CLI command exist to sort node manually.