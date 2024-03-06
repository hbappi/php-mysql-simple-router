<?php
class AppController extends Controller
{

    public function paginateApp(\Request $req, \Response $res)
    {

        // $payload = parent::getJsonBody();
        $payload = $req->getJsonBody();

        // return $payload;

        parent::dbExec('app__paginate', $payload, $res);


        // $result =  parent::dbExec('category__paginate', $payload); // returns ['ret_data', 'error']
        // $res->setBody($result)->end();
    }
    public function upsertApp(\Request $req, \Response $res)
    {

        $payload = $req->getJsonBody();

        $categories = $payload['apps'] ?? [];

        foreach ($categories as $category) {
            $result = parent::dbExec('app__upsert', $category);

            if ($result['error'] ?? false) {
                return $res->json($result)->end();
            }
        }

        $res->json([
            'ret_data' => 'success'
        ])->end();
    }
    public function deleteApp($req, $res)
    {
        $payload = $req->getJsonBody();

        $result = parent::dbExec('app__delete', $payload);
        
        if ($result['error'] ?? false) {
            return $res->json($result)->end();
        }

        $res->json([
            'ret_data' => 'success'
        ])->end();
    }
}
