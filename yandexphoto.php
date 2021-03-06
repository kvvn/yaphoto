<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Image CMS
 * Класс для получения фотографий которые сохранены в Я.Фотки для показа их на сайте.
 */

class Yandexphoto extends MY_Controller {
	public $settings = array();
	
	public function __construct()
    {
        parent::__construct();

        $this->load->module('core');

        // Загрузка настроек.
        $this->load_settings();
    }
	public function index($id=NULL){ // main function
		if ($id==NULL){
			$albums = $this->GetAlbums('kievfootball');  //TODO get username from settings
			//print_r ($albums);
			if (is_array($albums))
				$this->template->add_array(array('albums'=>$albums, 'quantity'=>count($albums)));
			$this->display_tpl('albums');
		} else {
			$photos = $this->GetPhotos('kievfootball', $id); //TODO get username from settings
			if (is_array($photos))
				$this->template->add_array(array('photos'=>$photos, 'quantity'=>count($photos)));
			$this->display_tpl('album');
		}
	}
	
	function GetAlbums($username){
		$url = 'http://api-fotki.yandex.ru/api/users/'.$username.'/albums/published/'; // URL ресурса с колекцией всех фото пользователя
		$xml = @file_get_contents($url); // получаем atom feed 
		$data = @simplexml_load_string($xml); // разбираем atom
		
		if ($data){
			$album = array();
			$i = 0;
			foreach($data->entry as $item){
				
				// album id 
				$album_id = $item->id;
				$album_id = explode(':', $album_id);
				$album_id = $album_id[count($album_id)-1];
				// album title 
				$album_title = $item->title;
				// album cover
				$temp_cover = $item->xpath('*[@size="S"]');
				//print_r($temp_cover);
				$album_cover = (string)$temp_cover[0]->attributes()->href;
				
				$album[$i] = array(
					'id' => $album_id,
					'title' => $album_title,
					'cover' => $album_cover
				);
				
				$i++;
			}
			
			return $album;
		} else {
			return 0;
		}
	}
	function GetPhotos($username, $id){
		$url = 'http://api-fotki.yandex.ru/api/users/'.$username.'/album/'.$id.'/photos/'; // URL ресурса с колекцией всех фото пользователя
		$xml = @file_get_contents($url); // получаем atom feed 
		$data = @simplexml_load_string($xml); // разбираем atom
		
		if ($data){
			$photos = array();
			$i = 0;
			foreach($data->entry as $item){
				$photoid = $item->id;
				$photoid = explode(':', $photoid);
				$photoid = $photoid[count($photoid)-1];
				
				$phototitle = $item->title;
				
				$temp_preview = $item->xpath('*[@size="S"]');
				$preview = (string)$temp_preview[0]->attributes()->href;
				
				$image = $item->content[0]->attributes()->src;
				
				$photos[$i] = array(
					'photoid' => $photoid,
					'phototitle' => $phototitle,
					'preview' => $preview,
					'image' => $image
				);
				$i++;
			}
			return $photos;
		} else {
			return 0;
		}
	}
	function getAllFoto($username){
		$url = 'http://api-fotki.yandex.ru/api/users/'.$username.'/photos/'; // URL ресурса с колекцией всех фото пользователя
		$xml = @file_get_contents($url); // получаем atom feed 
		$date = @simplexml_load_string($xml); // разбираем atom
 
		if ($date) {                            
			$foto = array();   
			$i = 0;
			// перебираем все entry элементы
			foreach ($date->entry as $item) {
				// id фотографии
				$foto_id = $item->id;
				$foto_id = explode(':', $foto_id);
				$foto_id = $foto_id[count($foto_id)-1];
 
				// title фотографии
				$foto_title = $item->title;
 
				// Время создания фотографии
				$foto_published_date = (string)$item->published;
 
				// Время последнего значимого с точки зрения системы изменения фотографии
				$foto_updated_date = (string)$item->updated;
 
				// Флаг, запрещающий показ оригинала фотографии:
				$temp_foto_hide_original = $item->xpath('f:hide_original');
				$foto_hide_original = (string)$temp_foto_hide_original[0]->attributes()->value;
 
				// Ссылка на web-страницу фотографии в интерфейсе Яндекс.Фоток
				$foto_ya_link = $item->xpath('*[@rel="alternate"]');
				$foto_ya_link = (string)$foto_ya_link[0]->attributes()->href;
 
				// Ссылка на графический файл фотографии: (!!! БЕЗ ОКОНЧАНИЯ !!!)
				$foto_src = substr($item->content[0]->attributes()->src, 0, -2);
 
				$foto[$i] = array(
					'id' => $foto_id,
					'title' => $foto_title,     
					'published_date' => $foto_published_date,
					'updated_date' => $foto_updated_date,
					'hide_original' => $foto_hide_original,
					'ya_link' => $foto_ya_link,
					'src' => $foto_src    
				);
 
				$i++;          
			} 
 
			return $foto;       
		} else {
			return 0;
		}
	}
	
	private function load_settings(){ // load settings
	}
	
	public function _install()
    {
        if($this->dx_auth->is_admin() == FALSE) exit;

        // Включаем доступ к модулю по URL
        $this->db->limit(1);
        $this->db->where('name', 'yandexphoto');
        $this->db->update('components', array('enabled' => 1));
    }
	private function display_tpl($file = '')
    {
       $this->template->add_array(array(
                'content' => $this->fetch_tpl($file),
            ));

        if (file_exists(realpath(dirname(__FILE__)).'/templates/public/main.tpl'))
        {
            $file = realpath(dirname(__FILE__)).'/templates/public/main.tpl';  
		    $this->template->display('file:' . $file);
        }
        else
        {
            // Use main site template
            $this->template->show();
            exit;
        }
	}
	
	private function fetch_tpl($file = '')
	{
        $file =  realpath(dirname(__FILE__)).'/templates/public/'.$file.'.tpl';  
		return $this->template->fetch('file:'.$file);
	}

}
