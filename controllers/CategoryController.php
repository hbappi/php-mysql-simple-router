<?php
class CategoryController extends Controller
{

    public function paginate(\Request $req, \Response $res)
    {

        // $payload = parent::getJsonBody();
        $payload = $req->getJsonBody();

        // return $payload;

        parent::dbExec('category__paginate', $payload, $res);


        // $result =  parent::dbExec('category__paginate', $payload); // returns ['ret_data', 'error']
        // $res->setBody($result)->end();
    }
    public function upsert(\Request $req, \Response $res)
    {

        $payload = $req->getJsonBody();

        $categories = $payload['categories'] ?? [];

        foreach ($categories as $category) {
            $result = parent::dbExec('category__upsert', $category);

            if ($result['error'] ?? false) {
                return $res->json($result)->end();
            }
        }

        $res->json([
            'ret_data' => 'success'
        ])->end();
    }
    public function delete($req, $res)
    {
        $payload = $req->getJsonBody();

        $result = parent::dbExec('category__delete', $payload);

        if ($result['error'] ?? false) {
            return $res->json($result)->end();
        }

        $res->json([
            'ret_data' => 'success'
        ])->end();
    }
}
