<?php
try{
    require_once 'boot.php';
	
	
	$application = new \Phalcon\Mvc\Application();
	$application->setDI($di);
	echo $application->handle()->getContent();
} catch (\Phalcon\Mvc\Dispatcher\Exception $e) {
    header("HTTP/1.0 404 Not Found");
} catch (Phalcon\Exception $e) {
	echo $e->getMessage();
} catch (PDOException $e){
	echo $e->getMessage();
}catch (Exception $e){
	echo $e->getMessage();
}
?>
