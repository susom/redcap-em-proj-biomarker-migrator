<?php


namespace Stanford\ProjBiomarkerMigrator;


class ConsentHandler
{


    public function foo() {
        Survey::archiveResponseAsPDF($record, $_GET['event_id'], $_GET['page'], $_GET['instance']);

    }
}