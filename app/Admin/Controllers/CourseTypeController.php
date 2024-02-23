<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\CourseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

class CourseTypeController extends AdminController
{
    //
	//creating menu sub-menu type as tree manner
	public function index(Content $content){
		
		$tree = new Tree(new CourseType);
		return $content->header('Course Type')->body($tree);
	}
	
	//just for view..that is called when we click on the three dots and click on the "show"
	 protected function detail($id)
    {
        $show = new Show(CourseType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Category'));
        $show->field('description', __('Description'));
        $show->field('order', __('Order'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
		

        return $show;
    }
	
	//just for creating a new item and submitted
	  protected function form()
    {
		//dd("create or edit");//"dd() is used for debug purposes,"dd" full meaning is dump and die"
        $form = new Form(new CourseType());
		//for making a drop down options for selecting the course types
		$form->select('parent_id',__('Parent Category'))->options((new CourseType())::selectOptions());//:: called scope resolution
		//for the course name or category
		$form->text('title',__('Title'));
		//for making a text area box to make it to take a lot of text as description
		$form->textarea('description',__('Description'));
		//for taking how many order we need as number like integer
		$form->number('order',__('Order'));
        return $form;
    }
}
