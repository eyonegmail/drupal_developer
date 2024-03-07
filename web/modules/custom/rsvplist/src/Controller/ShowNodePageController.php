<?php

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\node\Entity\Node;

/**
 * Provide Node Page.
 */
class ShowNodePageController extends ControllerBase {

  /**
   * ノードページを表示するコントローラー.
   *
   * @param string $code
   *   追加のパラメーター.
   */
  public function showNodePage($code = NULL) {
    $node = \Drupal::routeMatch()->getParameter('nid');
    if (is_null($node)) {
      throw new NotFoundHttpException();
    }
    $nid = $node->id();
    // パスからURLオブジェクトを生成する場合.
    // $url = Url::fromUri('internal:/node/' . $nid, ['code' => $code]);
    $url = Url::fromUri('internal:/node/' . $nid, ['query' => ['code' => $code]]);
// $url = Url::fromRoute('internal', ['node' => $nid], ['query' => ['code' => $code]]);
    // return new RedirectResponse('/node/3');

    // // リダイレクトレスポンスの生成.
    $redirect_response = new RedirectResponse($url->toString());

    // // リダイレクト実行.
    $redirect_response->send();

    // $build = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'full');

    // // レンダリングされた HTML を取得します。
    // $output = \Drupal::service('renderer')->renderRoot($build);
  
    // // レスポンスを作成して返します。
    // return new Response($output);
  }

}
