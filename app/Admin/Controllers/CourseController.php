<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\CourseType;
use App\Models\Course;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

class CourseController extends AdminController
{
   
	protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('Id'));
        $grid->column('user_token', __('Teacher'))->display(function($token){
			//for further use, we can create any method here or any operation here
		
		});
        $grid->column('name', __('Name'));
        $grid->column('thumbnail', __('Thumbnail'));
        $grid->column('video', __('Video'));
        $grid->column('description', __('Description'));
        $grid->column('type_id', __('Type id'));
        $grid->column('price', __('Price'));
        $grid->column('lesson_num', __('Lesson num'));
        $grid->column('video_length', __('Video length'));
        $grid->column('follow', __('Follow'));
        $grid->column('score', __('Score'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }
	
	
	//just for view..that is called when we click on the three dots and click on the "show"
	 protected function detail($id)
    {
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_token', __('Teacher'));
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('video', __('Video'));
        $show->field('description', __('Description'));
        $show->field('type_id', __('Type id'));
        $show->field('price', __('Price'));
        $show->field('lesson_num', __('Lesson num'));
        $show->field('video_length', __('Video length'));
        $show->field('follow', __('Follow'));
        $show->field('score', __('Score'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }
	
	//just for creating a new item and submitted
	  protected function form()
    {
		//dd("create or edit");//"dd() is used for debug purposes,"dd" full meaning is dump and die"
        $form = new Form(new Course());
		//means, in the "courses" table's in the field column the given name data will be saved
		$form->text('name',__('Name'));
		$result = CourseType::pluck('title','id');//here we can see that the left side value 'title' is shown with matching with the 'id' value
		//$result = CourseType::pluck('id','title');
		
		$form->select('type_id',__('Category'))->options($result);
		$form->image('thumbnail',__('Thumbnail'))->uniqueName();
		//file is used for video,pdf,doc and other format
		$form->file('video',__('Video'))->uniqueName();
		//decimal format helps in retrieving float type data from database
		$form->decimal('price',__('Price'));
		$form->number('lesson_num',__('Lesson number'));
		$form->number('video_length',__('Video length'));
		
		//For posting, who is posting
		$result = User::pluck('name','token');
		//dd($result);//'dd' used for debugging purposes
		$form->select('user_token',__('Teacher'))->options($result);
		$form->display('created_at',__('Created at'));
		$form->display('updated_at',__('Updated at'));
		
		//dd($result);//used for debugging purposes
		//for making a drop down options for selecting the course types
		//$form->select('parent_id',__('Parent Category'))->options((new CourseType())::selectOptions());//:: called scope resolution
		//for the course name or category
		//$form->text('title',__('Title'));
		//for making a text area box to make it to take a lot of text as description
		$form->textarea('description',__('Description'));
		//for taking how many order we need as number like integer
		//$form->number('order',__('Order'));
        return $form;
    }
}
