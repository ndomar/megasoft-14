package com.megasoft.entangle;

import java.io.ByteArrayOutputStream;

import android.annotation.SuppressLint;
import android.app.Fragment;
import android.content.ContentResolver;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;

@SuppressLint("NewApi") 
public class PhotoUploaderFragment extends Fragment{
	
	private static final int REQUEST_CODE = 1;
	private static final int RESULT_OK = 1;
	private ImageView icon;
	private Button button;
	private String encodedImage;
	private ContentResolver contentResolver;
	
	public static PhotoUploaderFragment getInstance(ContentResolver contentResolver){
		PhotoUploaderFragment fragment = new PhotoUploaderFragment();
		fragment.setContentResolver(contentResolver);
		return fragment;
	}
	
	public void setIcon(ImageView icon){
		this.icon = icon;
	}
	
	public void setContentResolver(ContentResolver contentResolver){
		this.contentResolver = contentResolver;
	}
	
	public void setButton(Button button){
		this.button = button;
	}
	
	public ImageView getIcon(){
		return icon;
	}
	
	public ContentResolver getContentResolver(){
		return contentResolver;
	}
	
	public Button getButton(){
		return button;
	}
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.upload_photo_fragement, container, false);
        
        setIcon((ImageView) view.findViewById(R.id.icon));
        
        Button iconButton = (Button) view.findViewById(R.id.iconButton);
        setButton(iconButton);
       
        iconButton.setOnClickListener(new OnClickListener(){
        	public void onClick(View view){
        		chooseIcon();
        	}
        });
        
        return view;
	}
	

	public void chooseIcon(){
		startActivityForResult(new Intent(Intent.ACTION_PICK,
				android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI),
				REQUEST_CODE);
		
		
	}
	
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == RESULT_OK && requestCode == REQUEST_CODE
				&& data != null) {
			Bitmap bitmap = getPhotoPath(data.getData());
			ImageView imageView = getIcon();
			imageView.setImageBitmap(bitmap);
			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			bitmap.compress(Bitmap.CompressFormat.JPEG, 100, baos);
			byte[] byteArray = baos.toByteArray();
			encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);
		}
	}
	
	public Bitmap getPhotoPath(Uri uri) {
		String[] projection = { android.provider.MediaStore.Images.Media.DATA };
		Cursor cursor = getContentResolver().query(uri, projection, null, null,
				null);
		int columnIndex = cursor.getColumnIndexOrThrow(projection[0]);
		cursor.moveToFirst();
		String filePath = cursor.getString(columnIndex);
		cursor.close();
		Bitmap bitmap = BitmapFactory.decodeFile(filePath);
		return bitmap;
	}
}
