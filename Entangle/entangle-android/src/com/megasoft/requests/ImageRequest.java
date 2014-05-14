package com.megasoft.requests;

import android.content.Context;
import android.widget.ImageView;

import com.squareup.picasso.Picasso;

/**
 * The class responsible for viewing the image from the cache if it is there and
 * download it if it's a cache miss.
 * 
 * @author MohamedBassem
 */
public class ImageRequest{
	public ImageRequest(String url,Context context,ImageView imageView){
		Picasso.with(context).load(url).into(imageView);
	}
}
