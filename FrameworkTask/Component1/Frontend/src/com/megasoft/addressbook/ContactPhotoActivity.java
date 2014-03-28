package com.megasoft.addressbook;

import android.app.Activity;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.ImageView;
import android.widget.Toast;

import com.megasoft.requests.PostImageRequest;
import com.sun.xml.internal.fastinfoset.algorithm.IntEncodingAlgorithm;

public class ContactPhotoActivity extends Activity {
	
	private final static int RESULT_LOAD_IMAGE = 1;
	
	String imagePath;
	

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_contact_photo);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.contact_photo, menu);
		return true;
	}
	
	public void getPhoto(View view){
		 Intent i = new Intent(
                 Intent.ACTION_PICK,
                 android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
         startActivityForResult(i, RESULT_LOAD_IMAGE);
	}
	
	@Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
         
        if (requestCode == RESULT_LOAD_IMAGE && resultCode == RESULT_OK && null != data) {
            Uri selectedImage = data.getData();
            String[] filePathColumn = { MediaStore.Images.Media.DATA };
 
            Cursor cursor = getContentResolver().query(selectedImage,
                    filePathColumn, null, null, null);
            cursor.moveToFirst();
 
            int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
            String picturePath = cursor.getString(columnIndex);
            cursor.close();
             
            ImageView imageView = (ImageView) findViewById(R.id.imgView);
            imageView.setImageBitmap(BitmapFactory.decodeFile(picturePath));
            imagePath = picturePath;            
         
        }
	}
	
	public void sendPhoto(View view){
		new PostImageRequest(){
			protected void onPostExecute(String response){
				Log.e("test","test1");
				if(!this.hasError()){
					success(true);
				}else{
					success(false);
				}
			}
		}.execute("http://megatweet.apiary-mock.com/test",this.imagePath);
		
	}
	
	public void success(boolean val){
		if(val){
			Toast.makeText(this, "Successed",Toast.LENGTH_SHORT).show();;
		}else{
			Toast.makeText(this, "Failed",Toast.LENGTH_SHORT).show();
		}
	}

}
