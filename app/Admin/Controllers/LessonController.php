<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LessonController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Lesson';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Lesson());

        $grid->column('id', __('Id'));
        $grid->column('course_id', __('Course'))->display(function($id){
			$item = Course::where('id','=',$id)->value('name');
			return $item;
		});
        $grid->column('name', __('Name'));
        $grid->column('thumbnail', __('Thumbnail'))->image('',50,50);
        $grid->column('description', __('Description'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Lesson::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('course_id', __('Course id'));
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('description', __('Description'));
        $show->field('video', __('Video'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Lesson());

        //$form->number('course_id', __('Course id'));
        $form->text('name', __('Name'));
		$result = Course::pluck('name','id');//pick a name based on the id of the courses model and assign to the course_id field of the lessons model after tapping the save button from the backend
		$form->select('course_id',__('Courses'))->options($result);//So, when the form is submitted, Laravel knows to take the selected course ID and save it into the course_id field of the Lesson model in the database.
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->textarea('description', __('Description'));
		
		//Within this table, it adds input fields for name, thumbnail, and url attributes of the video model. Each of these fields is set to be required.
        $form->table('video', function($form){
			//in this table the three fields are in json format and will save in json format in the database and they are not avaiable in the database as field
			//so we need to tell the database that we will pass them as json format and for this we need some code addition in the Lesson model, so go to Lesson model
			//and see what piece of codes should we have
			$form->text('name')->rules('required');
			$form->image('thumbnail')->uniqueName()->rules('required');
			$form->file('url')->rules('required');
		});
		$form->display('created_at',__('Created at'));
		$form->display('updated_at',__('Updated at'));

        return $form;
    }
}
