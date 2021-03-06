<?php

return [
    'common_error' => 'エラーが発生しました。',
    'common_no_result' => 'データがありません。',
    'common_no_result2' => ':nameがありません 。',
    'save_error'   => ':nameの保存中にエラーが発生しました。',
    'save_success' => ':nameを正常に保存しました。',
    'update_success' => ':nameを正常に更新しました。',
    'update_success_scenario' => 'シナリオ「:name」を正常に更新しました。',
    'update_error'   => ':nameの更新中にエラーが発生しました。',
    'not_record' => 'データがありません。',
    'delete_error' => ':nameの削除中にエラーが発生しました。',
    'delete_success' => ':nameが削除されました。',
    'exiting_error'=> ':nameは存在していません。',
    'model_delete_confirm' => '本当に削除しますか。',
    'modal_delete_success' => '正常に削除しました。',
    '404_msg'              => '404 ページが見つかりません。',
    '500_msg' => 'エラーが発生しました。再度お試しください。',
    'image_not_exist'    => 'イメージファイルが存在しません。',
    'image_create_error'        => 'イメージ作成途中でエラーが発生しました。',
    'url_not_exist'              => '入力された URL は確認できませんでした。URL を確認し、もう一度お試しください。',
    'already_exist_url'         => 'テストの各 URL は重複しないようにする必要があります。',
    'title_code_test_script'   => 'オリジナルページの冒頭にある head 開始タグの直後に次のテストコードを貼り付けます。',
    'title_code_script'         => 'トラッキングするすべてのウェブページに貼り付けます。',
    'running'     => '実施中',
    'stopping'    => '停止中',
    'template_file_not_exist' => 'テンプレートファイルが存在していません',
    'error_main'        => 'エラー',
    'error_network_connect'   => ':name Network, result null',
    'error_get_access_token'  => ':nameのアクセストークンは無効または期限切れです。',
    'error_get_info_acc'  => 'アカウント名は存在しません。',
    'token_expired'  => '無効または期限切れのトークンです。ダッシュボードからアカウント接続を行ってください。',
    'exists_error' => 'アカウントがすでに存在しています。',
    'not_exiting_service'  => 'SNSは存在しません。',
    'error_page_not_found'    => 'ページは存在しません。',
    'error_date_ranger' => '開始時間は終了時間より前でなければなりません。',
    'create_library_success' => 'Create library success!',
    'save_upload_success' => 'save upload :name success',
    'set_greeting_message_success' => 'Greeting messageを正常に設定できました。',
    'set_greeting_message_error'    => 'Greeting messageの設定中にエラーが発生しました。',
    'info_system_app'        => 'BotchanのFacebookアプリと連携しています。※ご自身のFacebookアプリを利用する場合は下記のボタンを押してください。',
    'info_origin_app'        => 'ご自身のFacebookアプリを利用しています。',
    'set_origin_page_error'        => 'Set origin page error',
    'set_system_page_error'        => 'Set system page error',
    'set_origin_page_success'        => ' 独自フェースブックアプリ設定成功',
    'set_system_page_success'        => 'Set system page success',
    'model_bot_use_system_confirm'   => 'システムボットを適用しますか？',
    'info_customize_embed_code'      => 'Size can be resized by setting it to large or xlarge, and if you set color to white you can change it to blue on white background)',
    'create_notify_success' => 'プッシュメッセージを正常に保存しました。',
    'persistent_menu_not_create' => '固定メニューが設定されていません。',
    'menu_item_limit'    => '固定メニューの項目は:numberつまでしか設定できません。',
    'menu_sub_item_limit'    => '固定メニューの項目は5つまでしか設定できません。',
    'day_of_week_required' => '曜日は、必ず指定してください。',
    'sub_menu_not_empty' => 'サブメニューを選択した場合、下位レベルの設定は必須です。',
    'error_variable' => 'この変数名は既に存在しています。',
    'variable_create_success' => '正常に変数を追加しました。',
    'upload_success' => ':name 正常にファイルをアップロードしました。',
    'api_no_result' => 'APIのデータはありません。',
    'saved_success' => '正常に保存しました。',
    'greeting_message_support' => '※@（アットマーク）を入力して、変数の一覧が表示されます。',
    'library_no_result' => 'キーワードマッチ辞書のデータがありません。',
    'scenario_no_result' => 'シナリオのデータはありません。',
    'service_not_selected' => 'サービスはまだ指定されていません。',
    'service_not_registered' => '指定したサービスはまだ開発中です。',
    'library_exists_error' => '指定のグループ名は既に使用されています。',
    'group_create_success' => 'グループを正常に保存しました。',
    'import_success' => "インポートを実行しました。<br>正常：:success レコード<br>異常：:error レコード",
    'user_add_limit' => 'ユーザー数の上限を超えましたので、ユーザーの追加ができません。',
    'bot_add_limit' => 'ボット数の上限を超えましたので、ボットの追加ができません。',
    'user_add_limit_bot' => 'ボット数の上限を超えましたので、ユーザーの追加ができません。',
    'max_user_number_over' => '既に:countを',
    'error_agency_authority_change' => '代理店は既にユーザーを作成したため、権限を変更できません。',
    'error_client_authority_change' => '自分が作成したユーザーしか権限を変更できません。',
    'error_delete_agency' => '代理店はクライアントを持っていますので、削除できません。',
    'error_delete_user' => 'ユーザーはボットを持っているため、削除することはできません。',
    'error_max_user_number_setting' => '代理店は既に:countユーザーを作成したため、ユーザー数の上限に:count以上の数字を入力してください。',
    'error_max_bot_number_setting' => 'ユーザーは既に:countボットを作成したため、ボット数の上限に:count以上の数字を入力してください。',
    'error_user_change_only_client' => '一般ユーザーのみに対して担当者の編集が出来ます。',
    'error_user_created_not_exist' => '指定した担当者は存在していません。',
    'error_user_created_not_client' => '担当者は一般ユーザー以外のユーザーを指定してください。',
    'error_user_created_add_bot_limit' => '指定した担当者はボット数の上限に達成したため、担当者の変更が出来ません。',
    'error_user_created_add_user_limit' => '指定した担当者はユーザ数の上限に達成したため、担当者の変更が出来ません。',
    'error_invalid_access_token' => '※ボットのFacebookアクセストークンが無効になっています。',
    'white_domain_notice' => '※代理店のホワイトドメイン以外指定できません。',
    'domain_format_incorrect' => 'ドメイン名のフォーマットが間違っています。',
    'error_limit_domain' => 'リンクURLのドメイン名が許可されません。',
    'error_domain_not_format_or_not_valid' => '入力したホワイトリストドメインには不有効な正規表現か代理店のホワイリストドメイン以外のドメインがあります。再度確認してください。',
    'comment_bot_test-create' => '※テストボットで本番のボットに影響を与えることなくボットの変更をテストすることができます。',
    'model_change_white_list_domain_confirm' => 'ホワイリストドメインが異なっています。ホワイリストドメインを更新して付け替えますか。',
    'data_error' => 'データがありません。',
    'file_format_error' => 'フォーマットテンプレートは正しくありません。ご確認ください。',
    'channel_id_error'   => 'チャネルIDは正しくありません！',
    'access_token_error'   => 'アクセストークンは正しくありません！',
    'keyword_match_scenario_notice' => '※キーワードマッチはこのシナリオ内に適用されます。',
    'my_app_invalid_parameter' => '独自アプリの情報が不正です。',
    'my_app_invalid_page_id' => '現在ページのアクセストークンではありません。',
    'sticker_select_empty' => 'スタンプを選択してください。',
    'all_dialog_message' => '※全体適用とはキーワードマッチはずべてシナリオに適用されます。',
    'transfer_data_success' => 'データを正常に移行しました。',
    'bot_transfer_message' =>'※コピー先ボット「:name1」のデータは全削除してから、移行元ボット「:name2」のデータを複製します。',
    'source_name_error_msg' =>'「コピー元ボット」は正しくありません',
    'no_results_found' =>'見つかりません。',
    'error_page_not_exist' => 'ページはありません。',
    'error_delete_starting_scenario' => '開始シナリオが削除できません。',
    'slot_variable_notice' => '※全ての変数が値に設定されたとき、スロットアクションを実行します。',
    'api_variable_setting_no_result' => 'There is no API variable setting data.',
    'file_select_empty' => 'ファイルが選択されていません。',
    'mail_no_result' => 'メールテンプレートがありません。',
    'web_embed_domain_exist' => '指定の:nameは既に使用されています。',
    'send_text' => 'メッセージ入力',
    'exists_mail' => 'このメールアドレスは既に使われています。',
    'email_sign_up_success' => '入力されたメールアドレスにメールを送りました。記載されているURLにアクセスし、登録情報の入力へお進みください。',
    'register_success' => 'ユーザー登録が完了しました。',
    'bot_role_create_description' => '※ボット管理者として追加するユーザーのメールアドレスを入力してください。',
    'link_register_actived' => 'このページのリクエスト・トークンが無効です。使用済み、または期限切れの可能性があります。',
    'bot_role_confirm_information' => ':user_nameさんから:bot_nameの:bot_authorityになる招待がありました。',
    'bot_role_success_user_accept' => ':user_nameから:bot_nameの:bot_authorityになるリクエストを承認しました。',
    'bot_role_error_user_invite' => 'このユーザーが既に管理者として招待されています。',
    'bot_role_success_user_invite' =>  ':user_nameに:bot_nameの:bot_authorityとして追加するリクエストを送信しました。',
    'bot_role_success_user_ignore' => ':user_nameから:bot_nameの:bot_authorityになるリクエストを拒否しました。',
    'bot_role_error_no_persimmon_add_person' => '他のユーザーを管理者としてリクエストを出す権限が必要です。',
    'bot_role_add_limit_person_manager' => '※現在のプランでは、最大:max_person管理者まで追加できます。',
    'bot_role_error_add_limit_manager' => '現在のプランでは、最大:max_person管理者まで追加できます。',
    'order_success' => 'お支払いが完了しました。',
    'order_error' => 'エラーが発生しました。',
    'error_get_data_facebook' => 'Facebookページを取得する際、エラーが発生しました。',
    'change_plan_to_using_function' => '本機能のご利用には、料金プランの変更が必要です。',
    'json_format_invalid' => 'JSON形式のテンプレート又は構文が間違っています。',
    'line_success' => 'BOTの作成が完了しました。<br/>次はLINE Developersに必要項目を入力しましょう。',
    'chatwork_success' => 'BOTの作成が完了しました。<br/>次はCHATWORK Developersに必要項目を入力しましょう。',
    'error_add_card' => 'ご利用出来ないカードをご利用になったもしくはカード番号が誤っております。',
    'error_nlp_add_application' => 'An application with the same name already exists',
    'error_nlp_delete_request_invalid' => 'The request is invalid.',
    'error_nlp_request_key_invalid' => 'Access denied due to invalid subscription key. Make sure to provide a valid key for an active subscription.',
    'success_application_train' => "アプリの学習を実行しました。",
    'import_success2' => "インポートが完了しました。",
    'import_error' => "インポート中にエラーが発生しました。",
    'import_not_data' => "インポートするデータがありません。",
    'import_not_empty_intentname_text' => ":row行目: インテント名およびテキストが必須です。",
    'file_type_require' => '※ファイルには:typeタイプのファイルを指定してください。',
    'cannot_import_japanese_to_culture_english' => 'NLPアプリは英語の場合、日本語テンプレートがインポートできません。',
    'success_mark_all_read' => 'すべてのスレッドを既読にしました。',
    'key_enter_to_send' => 'Enterで送信',
    'key_ctrl_enter_to_line_break' => ' Press Ctrl + Enter to line break',
    'create_success_scenario_group' => 'シナリオグループ「:name」を正常に追加しました。',
    'create_success_scenario' => 'シナリオ「:name」を正常に追加しました。',
    'attach_variable_notice' => '※このシナリオを実施するユーザーの変数値は入力した固定値になります。',
    'group_transfer_message' => "※グループ「:group_name」のすべてシナリオをボット「:bot_name」にコピーします。",
    'bot_name_description' => 'ボット名はFacebook Mesengerの相⼿には表示されません。御社内での管理用の名前です。',
    'img_description' => 'アイコンはFacebookページのアイコンが使われます。',
    'service_facebook_description' => "Facebookメッセンジャーと接続するボットです。<br/>Facebookメッセンジャーは世界で平均月間利用者数1億2千万人(*)、日本国内でも平均月間利用者数1,000万人(**)を超えるメッセージプラットフォームです。",
    'service_line_description' => "LINEメッセンジャーと接続するボットです。<br/>LINEメッセンジャーは日本国内での平均月間利用者数が3,800万人(**)を超えるLINEアプリと統合されたメッセージプラットフォームです。",
    'service_web_embed_description' => "お客様の所有するウェブサイトの任意のページに貼り付けることができるチャットボットです。<br/>カスタマーサポート、コンシェルジュのような使い方が可能です。",
    'service_web_embed_efo_description' => "お客様の所有するウェブサイトの任意のページに貼り付けることができるフォーム特化型チャットボットです。<br/>資料請求、会員登録、見積依頼などの申し込みフォームの代わりとしてボットがフォーム入力をサポートします。",
    'service_zalo_description' => "ベトナムで広く使われているチャットサービスです。",
    'service_slack_description' => "海外でも特にIT業界に人気の高いチャットサービスです。",
    'service_chatwork_description' => "Chatworkはグループチャット、タスク管理、ファイル共有、ビデオ通話/音声通話等の機能が搭載されています。<br/>223ヵ国の16万3千以上の会社で採用されており、世界でもっとも人気な仕事用のコミュニケーションのツールです。",
    'service_not_develop' => "近日対応予定",
    'copy_to_user_success' => ":user_nameアカウントにボットを正常に移行しました。",
    'user_input_hover' => "ユーザ入力",
    'bot_input_hover' => "ボットの発言",
    'read_google_sheet_description' => 'ユーザーの発言をスプレッドシートの:column_userカラムに、ボット発言をスプレッドシートの:column_botカラムに指定してください。',
    "oauth_success" => 'スプレッドシートを正常に読み込みました。',
    "oauth_error" => 'スプレッドシートの読み込みができません。',
    'add_keyword_matching' => 'キーワード入力',
    'reference_google_spreadsheet' => 'Google スプレッドシート参照',
    'set_option_success' => "正常に設定しました。",
    'scenario_group_transfer_success' => ":name-groupシナリオグループを正常に:copy-botボットにコピーしました。",
    'webhook_token_not_config' => 'Webhookトークンはまだ設定されていません。',
    'chatwork_invalid_api_token' => 'APIトークンは無効です。',
    'google_spread_next_time' => '次回読込時刻: :next_time',
    'google_spreadsheet_note' => '※Googleスプレッドシート参照の場合、ユーザーの発言は:column_userカラムで、ボット発言は:column_botカラムです。',
    'demo_efo' => 'EFO DEMO',
    'demo_webchat' => 'WEBCHAT DEMO',
    'connect_scenario_after_keyword_matching' => '※キーワードマッチした後のシナリオ遷移を設定する',
    'order_not_exist' => 'Order not exist',
    'set_webhook_url_success' => 'Webhook URLを正常に設定しました。',
    'set_conversion_success' => 'コンバージョン設定が完了しました。',
    'error_add_gateway' => '決済ゲートウェイを追加することができませんでした。',
];
