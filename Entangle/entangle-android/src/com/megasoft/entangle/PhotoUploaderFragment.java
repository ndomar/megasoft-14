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
import android.view.ViewGroup;
import android.widget.ImageView;

@SuppressLint("NewApi") 
public class PhotoUploaderFragment extends Fragment{
	
	private static final int REQUEST_CODE = 1;
	private static final int RESULT_OK = 1;
	private ImageView icon;
	private String encodedImage;
	private ContentResolver contentResolver;
	
	public static PhotoUploaderFragment getInstance(ContentResolver contentResolver,
			ImageView icon){
		PhotoUploaderFragment fragment = new PhotoUploaderFragment();
		fragment.setContentResolver(contentResolver);
		fragment.setIcon(icon);
		return fragment;
	}
	
	public void setIcon(ImageView icon){
		this.icon = icon;
	}
	
	public void setContentResolver(ContentResolver contentResolver){
		this.contentResolver = contentResolver;
	}
	
	public ImageView getIcon(){
		return icon;
	}
	
	public ContentResolver getContentResolver(){
		return contentResolver;
	}
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        return inflater.inflate(R.layout.upload_photo_fragement, container, false);
    }
	
}
