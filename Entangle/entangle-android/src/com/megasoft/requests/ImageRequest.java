package com.megasoft.requests;

import android.content.Context;
import android.widget.ImageView;

import com.squareup.picasso.Picasso;


public class ImageRequest{
	public ImageRequest(String url,Context context,ImageView imageView){
		Picasso.with(context).load(url).into(imageView);
	}
}
