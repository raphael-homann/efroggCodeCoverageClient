# efroggCodeCoverageClient

usage :

    if(extension_loaded("xdebug")) {
        $cc_server = new efrogg\CodeCoverage\CoverageApiServer("http://code-coverage-server/api/");
        $cc_server->setAuth('############');
    
        $cc_client = new efrogg\CodeCoverage\CodeCoverageClient($cc_server);
        $cc_client -> setProjectName("uba-v2")
            -> setRootPath(realpath(RACINE_WWW))
            -> setGetParamName("CC")
            -> setCookieName("CC")
    //        -> setVerbose(1)
            -> handleTrigger();

        \efrogg\CodeCoverage\CodeCoverageClient::setInstance($cc_client);
    }
